<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', '部署系统')</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset("AdminLTE/bootstrap/css/bootstrap.min.css") }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <link rel="stylesheet" href="{{ asset("AdminLTE/dist/css/AdminLTE.min.css") }}">
    <link rel="stylesheet" href="{{ asset("AdminLTE/dist/css/skins/skin-blue.min.css") }}">

    <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('static/css/bootstrap-sortable.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('static/css/common.css')}}">
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="{{ asset('static/js/moment.min.js') }}"></script>

    @section('js')
    @show

    @section('css')
    @show


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition skin-blue fixed sidebar-mini yf sidebar-collapse">

@section('body')
@show

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.1.4 -->
<script src="{{ asset("AdminLTE/plugins/jQuery/jQuery-2.2.0.min.js") }}"></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{ asset("AdminLTE/bootstrap/js/bootstrap.min.js") }}"></script>

<script src="{{ asset("AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js") }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset("AdminLTE/dist/js/app.js") }}"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->


</body>
</html>