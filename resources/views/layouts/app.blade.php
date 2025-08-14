<html>
    <head>
        <title>{{ config('app.name') }} - {{ $title }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/dataTables.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
        
        @yield('css')
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/toastr.min.js') }}"></script>
        <script src="{{ asset('js/dataTables.min.js') }}"></script>
        <script src="{{ asset('js/script.js') }}"></script>
    </head>
    <body>
        @include('includes.flashMsg')
        <div id="throbber" style="display:none; min-height:120px;"></div>
        <div id="noty-holder"></div>
        <div id="wrapper">

        

           @include('includes.sidebar')

            <div id="page-wrapper">
                <div class="container-fluid">
                    <div id="loader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background: rgba(255,255,255,0.7); z-index:9999; text-align:center;">
                        <img src="{{ asset('images/loader.gif') }}" style="margin-top:20%;">
                    </div>
                    <div class="row" id="main" >
                        
                        @yield('content')
                    </div>

                </div>

            </div>

        </div>

        
    </body>
    <script type="text/javascript">
        $(document).ready(function () {

            $(document).on('change', '.file-input', function () {
                const file = this.files[0]; 
                const previewContainer = $(this).closest('.col-md-4').find('.file-preview');

                previewContainer.empty();

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const wrapper = $('<div class="image-box new-image">');
                        const img = $('<img>').attr('src', e.target.result).css({
                            width: '100%',
                            maxHeight: '200px',
                            objectFit: 'cover'
                        });
                        const removeBtn = $('<button type="button" class="btn-remove-preview btn btn-sm btn-danger mt-2">x</button>');

                        removeBtn.on('click', function () {
                            $(wrapper).remove();
                            $('.file-input').val(''); 
                        });

                        wrapper.append(img, removeBtn);
                        previewContainer.append(wrapper);
                    };
                    reader.readAsDataURL(file);
                }
            });

            $(document).on('click', '.btn-remove-preview', function () {
                $(this).closest('.image-box').remove();
                $('.file-input').val('');
            });

        });
    </script>
    @yield('js')
</html>