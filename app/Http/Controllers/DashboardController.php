<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function mydashboard(){
        $data['title'] = 'Dashboard';
        return view('dashboard',$data);
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login')->with('success','Logout Success');
    }
}