@extends('layouts.app')

@section('content')
<div class="card mt-5">
    <h3 class="card-header p-3">Add Announcement</h3>

    <div class="card-body">
        <form method="POST" action="{{ route('announcements.store') }}" class="common_form" data-redirect="{{ route('announcements.index') }}">
            @csrf
            @include('announcements.form')
        </form>
    </div>
</div>
@stop
