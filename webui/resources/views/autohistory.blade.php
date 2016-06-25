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
                    @foreach($jenkinsjob as $Jenkinsconfig)
                        <th class="result_picture">{{ $Jenkinsconfig['JenkinsJobNameCn'] }}</th>
                    @endforeach
                    <th style="width:30px">部署时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->guid }}</td>
                        <td>{{ $item->hostsdeployed }}</td>
                        @for ($i = 0; $i < count($jenkinsjob); $i++)
                            @if( $item->getStage($i)['result'] == null )
                                <td></td>
                            @else
                                <td class="result_picture"><a href="{{ 'http://ci.mars.changbaops.com/job/' . $item->getStage($i)['jobname'] . '/' . $item->getStage($i)['jobid'] }}"><img src='{{ asset($item->getStage($i)['result']) }}'/></a></td>
                            @endif
                        @endfor
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