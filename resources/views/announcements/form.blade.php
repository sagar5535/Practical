@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-selection--multiple{
        height: 34px !important;
    }
</style>
@stop

<div class="row" style="margin-bottom: 10px;">
    <div class="col-md-6">
        <label for="user_ids" class="form-label">Assign To <span class="text-danger">*</span></label>
        <select name="user_ids[]" id="user_ids" class="form-control select2" multiple>
            @foreach($users as $user)
                <option value="{{ $user->id }}" 
                    @isset($selectedUsers)
                        {{ in_array($user->id, $selectedUsers) ? 'selected' : '' }}
                    @endisset
                >{{ $user->first_name.' '.$user->last_name }} ({{ $user->role->name ?? '' }})</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" id="title" class="form-control" placeholder="Enter title"
            value="{{ $announcement->title ?? '' }}">
    </div>
</div>

<div class="row" style="margin-bottom: 10px;">
    <div class="col-md-12">
        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
        <textarea name="description" id="message" class="form-control" placeholder="Enter message">{{ $announcement->description ?? '' }}</textarea>
    </div>
</div>


<button type="submit" class="btn btn-primary">Submit</button>

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
$('.select2').select2({
    placeholder: "Select Assign To",
    allowClear: true
});
</script>
@stop