<?php

$menuItems = [
    [
        'title' => 'Dashboard',
        'icon'  => 'fa fa-fw fa-home',
        'route' => 'dashboard',
        'roles' => [1, 2], 
    ],
    [
        'title' => 'Teachers',
        'icon'  => 'fa fa-fw fa-user-plus',
        'route' => 'teachers.index',
        'roles' => [1], 
    ],
    [
        'title' => 'Students',
        'icon'  => 'fa fa-fw fa-user-plus',
        'route' => 'students.index',
        'roles' => [1,2], 
    ],
    [
        'title' => 'Parents',
        'icon'  => 'fa fa-fw fa-user-plus',
        'route' => 'parents.index',
        'roles' => [1,2], 
    ],
    [
        'title' => 'Announcements',
        'icon'  => 'fa fa-fw fa-bullhorn',
        'route' => 'announcements.index',
        'roles' => [1, 2], 
    ],

];

?>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">

    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo.png') }}" alt="LOGO" height="45px" width="45px">
        </a>
    </div>

    <ul class="nav navbar-right top-nav">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                <b class="fa fa-angle-down"></b>
            </a>
            <ul class="dropdown-menu">
                <li><a href="{{ route('logout') }}"><i class="fa fa-fw fa-power-off"></i> Logout</a></li>
            </ul>
        </li>
    </ul>

    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">
            @foreach($menuItems as $item)
            @if(in_array(Auth::user()->role_id, $item['roles']))
            <li
                class="{{ request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'active' : '' }}">
                <a href="{{ route($item['route']) }}">
                    <i class="{{ $item['icon'] }}"></i> {{ $item['title'] }}
                </a>
            </li>
            @endif
            @endforeach
        </ul>
    </div>

</nav>