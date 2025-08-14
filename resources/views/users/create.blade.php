@extends('layouts.app')

@section('content')
<div class="card mt-5">
    <h3 class="card-header p-3">Add {{ ucfirst($viewFolder) }}</h3>
    <div class="card-body">
        <form method="POST" action="{{ route($viewFolder.'.store') }}" class="common_form" data-redirect="{{ route($viewFolder.'.index') }}" enctype="multipart/form-data">
            @csrf
            @include('users.form')
        </form>
    </div>
</div>
@stop
