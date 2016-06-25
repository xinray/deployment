@extends('common.base')

@section('body')
<div class="wrapper">

    <!-- Main Header -->
    @include('common.header')

    <!-- Left side column. contains the logo and sidebar -->
    @include('common.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @section('content')
        @show
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->

</div>
<!-- ./wrapper -->
@stop
