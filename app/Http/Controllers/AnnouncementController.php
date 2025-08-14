<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AnnouncementCreated;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Announcement::with('users');

            if (Auth::user()->role_id == 1) {
           
            } elseif (Auth::user()->role_id == 2) {
               
                $query->where(function($q) {
                    $q->where('created_by', Auth::id())
                      ->orWhereHas('users', function($qu) {
                          $qu->where('users.id', Auth::id());
                      });
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()

                ->addColumn('creator', function($row) {
                    // Only show for admin
                    if (Auth::user()->role_id == 1) {
                        return $row->creator ? $row->creator->first_name . ' ' . $row->creator->last_name : 'N/A';
                    }
                    return null; 
                })

                ->addColumn('recipients', function($row) {
                    return $row->users
                        ->filter(function($user) {
                            // For teacher login, show only students and parents
                            if (Auth::user()->role_id == 2) {
                                return $user->role_id != 2; 
                            }
                            return true;
                        })
                        ->map(function($user) {
                            return $user->first_name . ' ' . $user->last_name ?? '';
                        })
                        ->implode(', ');
                })


                ->addColumn('action', function($row) {
                    if ($row->created_by == Auth::id() && Auth::user()->role_id != '2') {
                        $btn  = '<a href="'.route('announcements.edit', $row->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" class="btn btn-sm btn-danger remove_entry" data-url="'.route('announcements.destroy', $row->id).'"><i class="fas fa-remove"></a>';
                        return $btn;
                    }
                    return '--';
                })

                ->filter(function ($query) use ($request) {
               
                    if (!empty($request->title)) {
                        $query->where('announcements.title', 'like', "%{$request->title}%");
                    }

                    if (!empty($request->recipient)) {
                        $query->whereHas('users', function($q) use ($request) {
                            $q->where('users.id', $request->recipient);
                        });
                    }

                    if (!empty($request->creator) && Auth::user()->role_id == 1) {
                        $query->where('created_by', $request->creator);
                    }
                 
                })

                ->rawColumns(['action','recipients'])
                ->make(true);
        }

        if (Auth::user()->role_id == 1) {
            $data['creatorArr'] = User::whereHas('announcements')
                                    ->get(['id', 'first_name', 'last_name'])
                                    ->unique('id')
                                    ->map(fn($u) => ['id' => $u->id, 'name' => $u->first_name . ' ' . $u->last_name])
                                    ->values();
        }

        $data['recipientArr'] = AnnouncementUser::with('user')
                                ->when(Auth::user()->role_id != 1, fn($q) => 
                                    $q->whereHas('user', fn($q2) => $q2->where('created_by', Auth::id()))
                                )
                                ->get()->pluck('user')->unique('id')
                                ->map(fn($user) => [
                                    'id' => $user->id,
                                    'name' => $user->first_name . ' ' . $user->last_name
                                ])->values();

        $data['title'] = 'Announcement';
        return view('announcements.index',$data);
    }

    public function create()
    {
        if (Auth::user()->role_id == 1) {
            $data['users'] = User::where('role_id', 2)->get();
        } elseif (Auth::user()->role_id == 2) {
            $data['users'] = User::whereIn('role_id', [3, 4])->where('created_by',Auth::user()->id)->get();
        }
        $data['title'] = 'Add Announcement';
        return view('announcements.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);


        $requestArr = $request->all();
        $announcement = Announcement::create($requestArr);
        $announcement->users()->attach($request->user_ids);

        // Send emails only if creator is a teacher
        if (Auth::user()->role_id == 2) {
            $users = User::whereIn('id', $request->user_ids)->get();

            foreach ($users as $user) {
                try {
                Mail::to($user->email)->send(new AnnouncementCreated($announcement, $user));
                Log::info("Email sent to {$user->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send announcement email to {$user->email}: " . $e->getMessage());
                }
            }
        }
        return response()->json(['status' => 'success', 'message' => 'Announcement created successfully!']);
    }

    public function edit(Announcement $announcement)
    {
        if ($announcement->created_by != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::user()->role_id == 1) {
            $users = User::where('role_id', 2)->get();
        } elseif (Auth::user()->role_id == 2) {
            $users = User::whereIn('role_id', [3,4])->where('created_by',Auth::user()->id)->get();
        }

        $selectedUsers = $announcement->users->pluck('id')->toArray();
        $title = 'Edit Announcement';
        return view('announcements.edit', compact('announcement', 'users', 'selectedUsers', 'title'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        if ($announcement->created_by != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $announcement->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        $announcement->users()->sync($request->user_ids);
        return response()->json(['status' => 'success', 'message' => 'Announcement updated successfully!']);
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->created_by != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $announcement->delete();
        return response()->json(['status' => 'success', 'message' => 'Announcement deleted successfully!']);
    }
}
