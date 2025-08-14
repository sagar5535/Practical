<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function(){
    Route::get('logout',[DashboardController::class, 'logout'])->name('logout');
    Route::get('dashboard',[DashboardController::class, 'mydashboard'])->name('dashboard');

    // Teacher module (role_id = 2)
    Route::resource('teachers', TeacherController::class)->middleware('module.access:2');

    // Student module (role_id = 3)
    Route::resource('students', StudentController::class)->middleware('module.access:3');

    // Parent module (role_id = 4)
    Route::resource('parents', ParentController::class)->middleware('module.access:4');

    Route::resource('announcements', AnnouncementController::class);
});

Route::middleware('guest')->group(function(){
    Route::get('/',[LoginController::class, 'index'])->name('login');
    Route::post('/process-login',[LoginController::class, 'postLogin'])->name('login.post');
});