@php
    $adminBrandName = $adminWebsiteSetting?->website_name ?: 'De Lamoure';

    $adminLogoUrl = $adminWebsiteSetting?->logo ? asset('storage/' . ltrim($adminWebsiteSetting->logo, '/')) : null;

    $adminFaviconUrl = $adminWebsiteSetting?->favicon
        ? asset('storage/' . ltrim($adminWebsiteSetting->favicon, '/'))
        : asset('assets/images/others/favicon.ico');
@endphp

<!DOCTYPE html>
<html lang="en" dir="ltr" class="no-js">

<head>
    <!-- Meta -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Admin Information -->
    <meta name="description" content="{{ $adminBrandName }} Admin Dashboard">
    <meta name="author" content="{{ $adminBrandName }}">
    <meta name="robots" content="noindex, nofollow">

    <!-- Title -->
    <title>@yield('title', $adminBrandName . ' Admin')</title>

    <!-- Theme Color -->
    <meta name="theme-color" content="#0d6efd">

    <!-- Favicon -->
    <link rel="icon" href="{{ $adminFaviconUrl }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Plugin Styles -->
    <link rel="stylesheet" href="{{ asset('admin/assets/plugin/datatables/responsive.datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/plugin/datatables/datatables.bootstrap5.min.css') }}">

    <!-- Project Styles -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/ebazar.style.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    @stack('styles')
</head>

<!-- plugin css file  -->
<link rel="stylesheet" href="{{ asset('admin/assets/plugin/datatables/responsive.datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/plugin/datatables/datatables.bootstrap5.min.css') }}">

<!-- project css file  -->
<link rel="stylesheet" href="{{ asset('admin/assets/css/ebazar.style.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div id="ebazar-layout" class="theme-blue">

        <!-- sidebar -->
        @include('admin.partials.sidebar')

        <!-- main body area -->
        <div class="main px-lg-4 px-md-4">

            <!-- Body: Header -->
            @include('admin.partials.header')

            @yield('content')

        </div>

    </div>

    <!-- Jquery Core Js -->
    <script src="{{ asset('admin/assets/bundles/libscripts.bundle.js') }}"></script>

    <!-- Plugin Js -->
    <script src="{{ asset('admin/assets/bundles/apexcharts.bundle.js') }}"></script>
    <script src="{{ asset('admin/assets/bundles/datatables.bundle.js') }}"></script>
    <script src="{{ asset('admin/assets/js/template.js') }}"></script>
    <script src="{{ asset('admin/assets/js/page/index.js') }}"></script>



    @stack('scripts')
</body>

</html>
