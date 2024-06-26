<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title> @yield('title') | {{ $app_settings ? $app_settings->name : config('app.name') }} </title>
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<meta name="Description" lang="en" content="Khagrachari Police Lines High School">
<meta name="author" content="Riyadh Ahmed">
<meta name="robots" content="index, follow">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Favicons -->
<link rel="icon" href="{{asset('/assets/images/favicon.png')}}" type="image/png" sizes="16x16">

<link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/themify-icons.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/magnific-popup.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/owl.carousel.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/menu.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/custom_admin_style.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/app-green.css') }}">


<!-- jQuery 3.3.1 -->
<script src="{{ asset('/assets/js/jquery-3.2.1.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('/assets/js/bootstrap.min.js') }}"></script>


<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
