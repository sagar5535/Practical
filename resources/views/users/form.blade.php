<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">First Name <span class="text-danger">*</span></label>
        <input type="text" name="first_name" class="form-control" value="{{ $formObj->first_name ?? '' }}" placeholder="First Name">
    </div>
    <div class="col-md-4">
        <label class="form-label">Last Name <span class="text-danger">*</span></label>
        <input type="text" name="last_name" class="form-control" value="{{ $formObj->last_name ?? '' }}" placeholder="Last Name">
    </div>
    <div class="col-md-4">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" value="{{ $formObj->email ?? '' }}" placeholder="Email">
    </div>
</div>

@if($viewFolder == 'teachers')
<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Password @if(!isset($formObj->id))<span class="text-danger">*</span>@endif</label>
        <input type="password" name="password" class="form-control" placeholder="Password">
    </div>
    <div class="col-md-4">
        <label class="form-label">Confirm Password @if(!isset($formObj->id))<span class="text-danger">*</span>@endif</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password">
    </div>
</div>
@endif

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Profile</label>
        <input type="file" name="profile" class="form-control file-input">
            <div class="file-preview">
                @if(isset($formObj->profile))
                    <div class="mt-2 image-box">
                        <img src="{{ asset('storage/'.$viewFolder.'/'.$formObj->id.'/'.$formObj->profile) }}" width="50">
                        <button type="button" class="btn-remove-preview">x</button>
                        <input type="hidden" name="keep_profile_image" value="{{ $formObj->id }}">
                    </div>
                @endif
            </div>
    </div>
</div>

<button type="submit" class="btn btn-primary" style="margin-top : 10px">Submit</button>
