<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        $data['title'] = 'Login';
        return view('login', $data);
    }

    public function postLogin(Request $request)     
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(Auth::attempt($request->only('email','password'))){
            return redirect()->route('dashboard')->with('success','Login Success'); 
        }

        return redirect()->back()->with('error','Invalid Login Cred..');
    }
}