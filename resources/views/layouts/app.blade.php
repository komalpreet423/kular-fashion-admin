<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Kular Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="{{ asset('assets/css/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/icons.min.css') }}">
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>

    @stack('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @vite('resources/js/app.js')
</head>

<body data-sidebar="dark">
    <div id="layout-wrapper">
        @include('layouts.components.header')
        @include('layouts.components.sidebar')

        <div class="main-content" id="{{ isset($isVueComponent) ? 'vue-components' : '' }}">
            @yield('content')
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        {{ date('Y') }} © Kular Fashion.
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('assets/libs/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery-ui-dist/jquery-ui.min.js') }}"></script>

    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

    @stack('scripts')

    <script>
        $(function() {
            $(document).on('click', '.delete-btn', function() {
                let source = $(this).data('source');
                let deleteApiEndpoint = $(this).data('endpoint');

                swal({
                    title: "Are you sure?"
                    , text: `You really want to remove this ${source}?`
                    , type: "warning"
                    , showCancelButton: true
                    , closeOnConfirm: false
                , }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: deleteApiEndpoint
                            , method: 'DELETE'
                            , data: {
                                '_token': '{{ csrf_token() }}'
                            }
                            , success: function(response) {
                                if (response.success) {
                                    swal({
                                        title: "Success!"
                                        , text: response.message
                                        , type: "success"
                                        , showConfirmButton: false
                                    })

                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                } else {
                                    let message = `Something went wrong!`;
                                    if (response.message) {
                                        message = response.message
                                    }

                                    swal({
                                        title: "Oops!"
                                        , text: message
                                        , type: "error"
                                        , confirmButtonText: 'Okay'
                                    })
                                }
                            }
                        })
                    }
                });
            })
        })

        $(document).ready(function() {
            // Restrict 'e', 'E', '+', and '-' for all input fields of type number
            $('input[type="number"]').on('keydown', function(event) {
                // Prevent invalid characters
                if (['e', 'E', '+', '-'].includes(event.key)) {
                    event.preventDefault();
                }
            });

            // Allow only valid numbers and a single decimal point
            $('input[type="number"]').on('input', function() {
                let value = $(this).val();

                // Replace invalid characters and allow one decimal point
                if (!/^\d*\.?\d*$/.test(value)) {
                    $(this).val(value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/, '$1'));
                }
            });
        });

    </script>
</body>

</html>
