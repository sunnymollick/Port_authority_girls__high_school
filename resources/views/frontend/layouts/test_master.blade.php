<!DOCTYPE html>
<html>
<head>
    @include('frontend.layouts.head')
</head>
<body>
<div class="container">
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>
    <div class="container m-top-60">
        <div class="row ">
            <div class="col-sm-12 col-md-12 p-right-40">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- Footer section -->
    <footer class="footer-section">
        @include('frontend.layouts.footer')
    </footer>
</div>
</body>
</html>