@extends('layouts.app')

@section('content')
<div class="card mt-5">
    <h3 class="card-header p-3">Edit {{ ucfirst($viewFolder) }}</h3>
    <div class="card-body">
        <form method="POST" action="{{ route($viewFolder.'.update', [Str::singular($viewFolder) => $formObj->id]) }}" class="common_form" data-redirect="{{ route($viewFolder.'.index') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('users.form')
        </form>
    </div>
</div>
@stop
