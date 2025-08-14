<?php

namespace App\Http\Controllers;

class TeacherController extends BaseUserController
{
    protected $role_id = 2;
    protected $viewFolder = 'teachers';
    protected $storageFolder = 'teachers';
    protected $hasPassword = true;
}
