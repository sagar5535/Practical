@extends('layouts.app')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@stop
@section('content')
<div class="card mt-5">
    <h3 class="card-header p-3">{{ ucfirst($viewFolder) }}</h3>

    @if((Auth::user()->role_id == 1 && $roleId == 2) || (Auth::user()->role_id == 2 && in_array($roleId, [3,4])))
    <a href="{{ route($viewFolder.'.create') }}" class="btn btn-primary">Add {{ Str::singular(ucfirst($viewFolder)) }}</a>
    @endif

    <div class="card-body">
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    @if(Auth::check() && Auth::user()->role_id == 1 && $roleId != 2)
                        <th>Teacher</th>
                    @endif
                    <th width="100px">Action</th>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="text" id="search_name" class="form-control" placeholder="search"></td>
                    <td><input type="text" id="search_email" class="form-control" placeholder="search"></td>
                    @if(Auth::check() && Auth::user()->role_id == 1 && $roleId != 2)
                        <td>
                            <select id="search_teacher" class="form-control">
                                <option value="">Select Teacher</option>
                                @foreach($teacherArr as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }}</option>
                                @endforeach
                            </select>
                        </td>
                    @endif
                    <td></td>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script type="text/javascript">
$(document).ready(function() {

    // Pass roleId from Blade to JS
    var roleId = @json($roleId);
    var isAdminView = @json(Auth::check() && Auth::user()->role_id == 1);
    var orderIndex = (isAdminView && roleId != 2) ? 5 : 4;

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        orderCellsTop: true,
        order: [[orderIndex, 'desc']],
        ajax: {
            url: "{{ route($viewFolder.'.index') }}",
            method: "GET",
            data: function(d) {
                d.name = $('#search_name').val();
                d.email = $('#search_email').val();
                if (isAdminView && roleId != 2) {
                    d.teacher = $('#search_teacher').val();
                }
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'full_name', name: 'full_name'},
            {data: 'email', name: 'email'},
            // Teacher column only for admin
            ...(isAdminView && roleId != 2 ? [{data: 'teacher_name', name: 'teacher_name'}] : []),
            {data: 'action', name: 'action', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at', visible: false}
        ]
    });

    $('#search_name, #search_email, #search_teacher').on('change keyup', function() {
        table.draw();
    });

});
</script>

@stop
