@extends('common.main')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('/static/css/dashboard.css') }}">
@stop

@section('js')
    <script type="text/javascript" src="{{ asset('static/js/bootstrap-sortable.js') }}"></script>
    <script type="text/javascript" src="{{ asset('static/js/itemselect.js') }}"></script>
@stop

@section('content')
    <div class="container">

        <div class="title">Dashboard</div>
        @for($product_num = 0; $product_num<count($product_name); $product_num++)
                <div class="box box-info">
                    <div class="box-header with-border">
                <h3 class="box-title">{{ $product_name[$product_num] }}</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                        <tr>
                            <th>HOST</th>
                            <th>GUID</th>
                            <th>BuildID</th>
                            <th>部署时间</th>
                            <th>状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>接口状态</td>
                            <td></td>
                            <td><span class="label label-info"></span></td>
                            <td>
                                <div class="sparkbar" data-color="#00c0ef" data-height="20"></div>
                            </td>
                            <td><img src={{ $monitor_url[$product_num] }}/></td>
                        </tr>
                        @if($items[$product_num] != null)
                        @foreach($items[$product_num] as $item)
                            <tr>
                                <td>{{ $item['hostsdeployed'] }}</td>
                                <td>{{ $item['guid'] }}</td>
                                <td>{{ $item['items']['buildid'] }}</td>
                                <td>
                                    <div class="sparkbar" data-color="#00c0ef" data-height="20">{{ $item['lastupdate'] }}</div>
                                </td>
                                <td></td>
                            </tr>
                        @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
                    <!-- /.box-body
                    <div class="box-footer clearfix">
                        <a href="javascript:void(0)" class="btn btn-sm btn-info btn-flat pull-left">Place New Order</a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Orders</a>
                    </div> -->
                    <!-- /.box-footer -->
                </div>
        @endfor

    </div>
@stop