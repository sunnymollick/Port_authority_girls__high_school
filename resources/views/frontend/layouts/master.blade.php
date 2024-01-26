<!DOCTYPE html>
<html>
<head>
    @include('frontend.layouts.head')
</head>
<body>
<div class="{{ $app_settings->layout ? '' : 'container' }}">
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>
    <!-- header section -->
    <header class="header-section">
        @include('frontend.layouts.header')
    </header>
    <!-- header section end-->
    <section class="main-section">
        @yield('content')
    </section>
    <!-- Footer section -->
    <footer class="footer-section">
        @include('frontend.layouts.footer')
        @include('backend.layouts.datatable')
    </footer>
</div>
</body>
</html>
