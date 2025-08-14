<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BaseUserController extends Controller
{
    protected $role_id;        
    protected $viewFolder;     
    protected $storageFolder;  
    protected $hasPassword = false;

    public function index(Request $request)
    {
        
        if ($request->ajax()) {
       
            $query = User::select(
                'users.*',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as full_name")
            )->where('role_id', $this->role_id)
            ->userAccess();

            if (Auth::check() && Auth::user()->role_id == 1 && $this->role_id != 2) {
                $query->addSelect(DB::raw("(SELECT CONCAT(u.first_name,' ',u.last_name) 
                    FROM users u WHERE u.id = users.created_by) as teacher_name"));
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('name', fn($row) => $row->full_name)
                ->addColumn('action', function ($row) {
                    // Admin buttons (for teacher module only)
                    if (Auth::user()->role_id == 1 && $this->role_id == 2) {
                        $btn  = '<a href="' . route($this->viewFolder.'.edit', [$this->singularName() => $row->id]) . '" class="edit btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>';
                        $btn .= '<a href="javascript:void(0)" class="delete btn btn-primary btn-sm remove_entry" style="margin-left:5px" data-url="' . route($this->viewFolder.'.destroy', [$this->singularName() => $row->id]) . '"><i class="fas fa-remove"></i></a>';
                        return $btn;
                    }

                    // Teacher buttons (for student/parent module only)
                    if (Auth::user()->role_id == 2 && in_array($this->role_id, [3,4])) {
                        $btn  = '<a href="' . route($this->viewFolder.'.edit', [$this->singularName() => $row->id]) . '" class="edit btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>';
                        $btn .= '<a href="javascript:void(0)" class="delete btn btn-primary btn-sm remove_entry" style="margin-left:5px" data-url="' . route($this->viewFolder.'.destroy', [$this->singularName() => $row->id]) . '"><i class="fas fa-remove"></i></a>';
                        return $btn;
                    }

                    // Otherwise no buttons
                    return '--';
                })

                ->filter(function ($query) use ($request) {
                    if (!empty($request->name)) {
                        $query->whereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ["%{$request->name}%"]);
                    }
                    if (!empty($request->email)) {
                        $query->where('users.email', 'like', "%{$request->email}%");
                    }
                    if (!empty($request->teacher) && Auth::check() && Auth::user()->role_id == 1 && $this->role_id != 2) {
                        $query->where('created_by', $request->teacher);
                    }
                })
                ->rawColumns(['action', 'name'])
                ->make(true);
        }

        // Pass teacher array for select box if admin
        if(Auth::user()->role_id == 1 && $this->role_id != 2){
            $data['teacherArr'] = User::where('role_id',2)->get();    
        }

        $data['title'] = ucfirst($this->viewFolder);

        return view('users.index', $data)
            ->with('viewFolder', $this->viewFolder)
            ->with('roleId', $this->role_id);
    }


    public function create()
    {
        $this->checkAccess();
        $data['title'] = 'Add ' . $this->singularName(ucfirst($this->viewFolder));
        return view("users.create",$data)->with('viewFolder', $this->viewFolder);
    }

    public function store(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users,email',
            'profile'    => 'nullable|mimes:jpg,jpeg,png|max:2048'
        ];

        if ($this->hasPassword) {
            $rules['password'] = 'required|min:6|confirmed';
            $rules['password_confirmation'] = 'required';
        }

        $request->validate($rules);

        $data = $request->all();
        $data['role_id'] = $this->role_id;

        if ($this->hasPassword && isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user = User::create($data);

        if ($request->hasFile('profile')) {
            $path = $request->file('profile')->store("{$this->storageFolder}/{$user->id}", 'public');
            $user->profile = basename($path);
            $user->save();
        }

        return response()->json(['status' => 'success', 'message' => ucfirst($this->viewFolder).' Created!!']);
    }

    public function edit($id)
    {
        $this->checkAccess();
        $data['formObj'] = User::find($id);
        $data['title'] = 'Edit ' . $this->singularName(ucfirst($this->viewFolder));
        return view("users.edit", $data)->with('viewFolder', $this->viewFolder);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $rules = [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users,email,' . $id,
            'profile'    => 'nullable|mimes:jpg,jpeg,png|max:2048'
        ];

        if ($this->hasPassword) {
            $rules['password'] = 'nullable|confirmed';
            $rules['password_confirmation'] = 'nullable';
        }

        $request->validate($rules);

        $data = $request->all();

        if ($this->hasPassword) {
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = bcrypt($data['password']);
            }
        }

        $keepProfile = $data['keep_profile_image'] ?? '';
        if (!$keepProfile) {
            Storage::disk('public')->delete("{$this->storageFolder}/{$user->id}/{$user->profile}");
            $data['profile'] = null;
        }

        if ($request->hasFile('profile')) {
            if ($user->profile) {
                Storage::disk('public')->delete("{$this->storageFolder}/{$user->id}/{$user->profile}");
            }
            $data['profile'] = basename($request->file('profile')->store("{$this->storageFolder}/{$user->id}", 'public'));
        }

        $user->update($data);

        return response()->json(['status' => 'success', 'message' => ucfirst($this->viewFolder).' Updated!!']);
    }

    public function destroy($id)
    {
        $this->checkAccess();
        $user = User::find($id);
        if (!empty($user->profile)) {
            Storage::disk('public')->delete("{$this->storageFolder}/{$user->id}/{$user->profile}");
        }
        $user->delete();

        return response()->json(['status' => 'success', 'message' => ucfirst($this->viewFolder).' Deleted !!']);
    }

    protected function singularName()
    {
        return rtrim($this->viewFolder, 's'); 
    }

    protected function checkAccess()
    {
        $userRole = Auth::user()->role_id;
        if ($userRole == 1 && $this->role_id != 2) {
            abort(403, 'Unauthorized action.');
        }

        if ($userRole == 2 && $this->role_id == 2) {
            abort(403, 'Unauthorized action.');
        }
    }

}
