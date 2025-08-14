$(function(){

    $('.common_form').on('submit',function(e){
        e.preventDefault();
        $('#loader').show();
        var formData = new FormData(this);
        let _this = this;
        $.ajax({
            url : $(_this).attr('action'),
            data : formData,
            type : 'POST',
            contentType: false,
            processData: false,
            success: function(result) {
                $('#loader').hide();
                if (result.status === 'success') {
                    toastr.success(result.message);
                    setTimeout(function() {
                        window.location.href = $(_this).data('redirect');
                    }, 1500);
                } else if (result.status === 'error') {
                    toastr.error(result.message);
                } else {
                    toastr.warning(result.message || 'Unexpected response.');
                }
            },
            error: function(xhr){
                $('#loader').hide();
                toastr.error(xhr.responseJSON.message);
            }
        });
      
    });

    $(document).on('click', '.remove_entry', function(e){
        if (confirm('Are you Sure ?')) {
            $.ajax({
                url : $(this).data('url'),
                type : 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: false,
                processData: false,
                success : function(result){
                    toastr.success(result.message);
                    $('.data-table').DataTable().draw();
                },
                error: function(xhr){
                    toastr.error(xhr.responseJSON.message);
                }
            });
        }
        return false;
        
    });
})    
   