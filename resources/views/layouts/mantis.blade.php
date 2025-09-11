<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->
<head>
    <title>{{ config('app.name', 'Base CRM') }} - @yield('title', 'Dashboard')</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Base CRM Management System">
    <meta name="keywords" content="CRM, Management, Dashboard, Admin">
    <meta name="author" content="Base CRM">

    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ asset('assets/mantis/images/favicon.svg') }}" type="image/x-icon">
    
    <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/fonts/tabler-icons.min.css') }}">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/fonts/feather.css') }}">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/fonts/fontawesome.css') }}">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/fonts/material.css') }}">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/mantis/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/mantis/css/style-preset.css') }}">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('styles')
</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body>
    @include('layouts.parts.loader')
    @include('layouts.parts.sidebar')
    @include('layouts.parts.topbar')

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            @yield('content')
        </div>
    </div>
    <!-- [ Main Content ] end -->

    @include('layouts.parts.modal')
    @include('layouts.parts.footer')

    <!-- Required Js -->
    <script src="{{ asset('assets/mantis/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/pcoded.js') }}"></script>
    <script src="{{ asset('assets/mantis/js/plugins/feather.min.js') }}"></script>
    
    @stack('scripts')
</body>
<!-- [Body] end -->
</html>
