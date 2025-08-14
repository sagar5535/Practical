@extends('layouts.app')

@section('css')
   
@stop
@section('content')
    <div class="col-sm-12 col-md-12 well" id="content">
        <h1>Welcome {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}!</h1>
    </div>

@stop
@section('js')
 
@stop