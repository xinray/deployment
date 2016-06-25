@extends('common.main')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('/static/css/item-table.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/static/css/bootstrap-select.css') }}">
@stop

@section('js')
    <script type="text/javascript" src="{{ asset('static/js/bootstrap-sortable.js') }}"></script>
    <script type="text/javascript" src="{{ asset('static/js/bootstrap-select.js') }}"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="{{ asset('static/js/itemselect.js') }}"></script>
@stop

@section('content')
    <div class="container">

        <div class="title">部署系统历史结果列表</div>
        <div class="firstline">
            <div class="select-stage">
                &nbsp;产品名&nbsp;&nbsp;
                <select class="selectpicker" onchange="select_type(this.value)">
                    <option @if(request('type') == '') selected="selected" @endif value="">全部</option>
                    @foreach(config('data')['product'] as $v)
                        <option @if(request('type') == $v['productid']) selected="selected" @endif value="{{ $v['productid'] }}">{{ $v['productid'] }}</option>
                    @endforeach
                </select>
            </div>
            <ul class="pageitem-num">
                <li @if(request('num') == '20') class="select" @endif><a href="{{ \App\Lib\Url::append('num', 20) }}">20</a></li>
                <li @if(request('num') == '50') class="select" @endif><a href="{{ \App\Lib\Url::append('num', 50) }}">50</a></li>
                <li @if(empty(request('num'))) class="select" @endif><a href="{{ \App\Lib\Url::del('num') }}">All</a></li>
            </ul>
        </div>
        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-striped table-hover sortable">
                <thead>
                <tr>
                    <th style="width:30px">ID</th>
                    <th style="width:30px">GUID</th>
                    <th style="width:30px">hostsdeployed</th>
                    <th class="result_picture">文件分发&Checksum&结果</th>
                    <th class="result_picture">API校验－Host&结果</th>
                    <th class="result_picture">API校验－线上&结果</th>
                    <th style="width:30px">部署时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->guid }}</td>
                        <td>{{ $item->hostsdeployed }}</td>
                        <td class="result_picture"><a href="{{ 'http://ci.mars.changbaops.com/job/' . $item->filedistribution_job . '/' . $item->filedistribution_id }}"><img src='{{ asset($item->filedistribution_result) }}'/></a></td>
                        <td class="result_picture"><a href="{{ 'http://ci.mars.changbaops.com/job/' . $item->hostverification_job . '/' . $item->hostverification_id }}"><img src='{{ asset($item->hostverification_result) }}'/></a></td>
                        <td class="result_picture"><a href="{{ 'http://ci.mars.changbaops.com/job/' . $item->Apitest_job . '/' . $item->Apitest_id }}"><img src='{{ asset($item->Apitest_result) }}'/></a></td>
                        <td>{{ $item->lastupdate }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="page">
            {!! $items->render() !!}
        </div>
    </div>
@stop