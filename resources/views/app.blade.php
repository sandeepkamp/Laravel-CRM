<!DOCTYPE html>



<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="{{ url('assets') }}" data-template="vertical-menu-template-free">

<head>

    <meta charset="utf-8" />

    <meta

    name="viewport"

    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />



    <title>@yield('title') - {{ config('app.name') }}</title>



    <meta name="description" content="" />



    <!-- Favicon -->

    <link rel="icon" type="image/x-icon" href="{{ url('assets/img/logo.png') }}" />



    <!-- Fonts -->

    <link rel="preconnect" href="https://fonts.googleapis.com" />

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link

    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"

    rel="stylesheet" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ url('assets/vendor/fonts/materialdesignicons.css') }}" />



    <!-- Menu waves for no-customizer fix -->

    <link rel="stylesheet" href="{{ url('assets/vendor/libs/node-waves/node-waves.css') }}" />



    <!-- Core CSS -->

    <link rel="stylesheet" href="{{ url('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />

    <link rel="stylesheet" href="{{ url('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />

    <link rel="stylesheet" href="{{ url('assets/css/demo.css') }}" />



    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{ url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ url('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">

    @yield('style')

    @stack('style')



    <!-- Page CSS -->



    <!-- Helpers -->

    <script src="{{ url('assets/vendor/js/helpers.js') }}"></script>

    <script src="{{ url('assets/js/config.js') }}"></script>

</head>

<body>

    <div class="layout-wrapper layout-content-navbar">

        <div class="layout-container">

            @include('layouts.sidebar')

            <div class="layout-page">

                @include('layouts.header')

                <div class="content-wrapper">

                    <div class="container-xxl flex-grow-1 container-p-y">

                        <h5>@yield('title')</h5>

                        <nav aria-label="breadcrumb">

                            @yield('breadcrumb')

                        </nav>

                        @yield('content')

                    </div>

                    @include('layouts.footer')

                    <div class="content-backdrop fade"></div>

                </div>

            </div>

        </div>

    </div>

    <div class="layout-overlay layout-menu-toggle"></div>

    <script src="{{ url('assets/vendor/libs/jquery/jquery.js') }}"></script>

    <script src="{{ url('assets/vendor/libs/popper/popper.js') }}"></script>

    <script src="{{ url('assets/vendor/js/bootstrap.js') }}"></script>

    <script src="{{ url('assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ url('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ url('assets/vendor/js/menu.js') }}"></script>



    <script src="{{ url('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>



    <script src="{{ url('assets/js/main.js') }}"></script>



    <script src="{{ url('assets/js/dashboards-analytics.js') }}"></script>
    <script src="{{ asset('assets/js/form-prevent-double-submit.js') }}"></script>
    <script>
        $(window).on('resize', function () {
            $('.dataTable').each(function () {
                $(this).DataTable().columns.adjust().responsive.recalc();
            });
        });
    </script>

    @yield('script')

    @stack('script')



    @if(session()->has('success'))

    <script type="text/javascript">

        Swal.fire({

            title: "{{ session()->get('success') }}",

            icon: "success",

            allowOutsideClick: false

        });

    </script>

    @endif



    @if(session()->has('error'))

    <script type="text/javascript">

        Swal.fire({

            title: "{{ session()->get('error') }}",

            icon: "error",

        });

    </script>

    @endif
    @php session()->forget('success'); session()->forget('error'); @endphp

</body>



</html>