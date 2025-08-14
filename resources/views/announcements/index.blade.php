@extends('layouts.app')

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@stop

@section('content')
<div class="card mt-5">
    <h3 class="card-header p-3">Announcements</h3>
    <a href="{{ route('announcements.create') }}" class="btn btn-primary">Add Announcement</a>

    <div class="card-body">
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Title</th>
                    <th>Message</th>
                    @if(Auth::check() && Auth::user()->role_id == 1)
                        <th>Creator</th>
                    @endif
                    <th>Recipients</th>
                    <th width="100px">Action</th>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="text" id="search_title" class="form-control" placeholder="Search Title"></td>
                    <td></td>
                    @if(Auth::check() && Auth::user()->role_id == 1)
                        <td>
                            <select name="creator_id" id="search_creator" class="form-control">
                                <option value="">Seelct Creator</option>
                                @foreach($creatorArr as $creator)
                                    <option value="{{ $creator['id'] }}">{{ $creator['name'] }}</option>
                                @endforeach
                            </select>
                        </td>
                    @endif
                    <td>
                        <select name="recipient_id" id="search_recipient" class="form-control">
                            <option value="">Seelct Recipient</option>
                            @foreach($recipientArr as $recipient)
                                <option value="{{ $recipient['id'] }}">{{ $recipient['name'] }}</option>
                            @endforeach
                        </select>
                    </td>
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

    var isAdminView = @json(Auth::check() && Auth::user()->role_id == 1);
    var orderIndex = (isAdminView) ? 6 : 5;

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        orderCellsTop: true,
        order: [[orderIndex, 'desc']],
        ajax: {
            url: "{{ route('announcements.index') }}",
            method: "GET",
            data: function(d) {
                d.title = $('#search_title').val();
                if(isAdminView) {
                    d.creator = $('#search_creator').val();
                }
                d.recipient = $('#search_recipient').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false},
            {data: 'title', name: 'title'},
            {data: 'description', name: 'description', orderable:false, searchable:false},
            ...(isAdminView ? [{data: 'creator', name: 'creator'}] : []),
            {data: 'recipients', name: 'recipients', orderable:false, searchable:false},
            {data: 'action', name: 'action', orderable:false, searchable:false},
            {data: 'created_at', name: 'created_at', visible: false},
        ]
    });

    $('#search_title, #search_creator, #search_recipient').on('keyup change', function() {
        table.draw();
    });
});
</script>
@stop
