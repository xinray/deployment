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

     <div class="title">版本库Build列表</div>
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
         <div class="select-stage">
             &nbsp;状态&nbsp;&nbsp;
             <select id="basic" class="selectpicker" onchange="select_stage(this.value)">
                <option @if(request('stage') == '') selected="selected" @endif value="">全部</option>
                <option @if(request('stage') == 'released') selected="selected" @endif value="released">已上线</option>
                <option @if(request('stage') == 'deleted') selected="selected" @endif value="deleted">已删除</option>
                <option @if(request('stage') == 'archived') selected="selected" @endif value="archived">已存档</option>
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
             <th>GUID</th>
             <th>产品名</th>
             <th>版本号</th>
             <th>SVN revision</th>
             <th style="width: 130px;word-break:break-all; word-wrap:normal;">checksum</th>
             <th>存档时间</th>
             <th style="width: 60px;">状态</th>
             <th>部署时间</th>
             <th>测试环境测试结果</th>
             <th>预上线环境测试结果</th>
             <th>操作</th>
          </tr>
       </thead>
       <tbody>
        @foreach($items as $item)
          <tr>
             <td>{{ $item->id }}</td>
             <td>{{ $item->productid }}</td>
             <td>{{ $item->buildid }}</td>
             <td>{{ $item->svnrevision }}</td>
             <td style="width: 130px; word-break:break-all; word-wrap:normal;">{{ $item->checksum }}</td>
             <td>{{ $item->archivetime }}</td>
             <td style="width: 60px;"><span class="{{ $item->status_background }}">{{ $item->status_name }}</span></td>
              @if( count($item->deployhistory) >= 1)
                  @foreach($item->deployhistory as $history)
                      <td>{{ $history->lastupdate }}</td>
                  @endforeach
              @else
                  <td></td>
              @endif
              @if($item->test_result != null)
                  @if($item->test_result->result_smoke != null)
                    <td><a href="{{ 'http://ci.mars.changbaops.com/job/' . $item->test_result->smokeresult_job . '/' . $item->test_result->smokeresult_id }}"><img src='{{ asset($item->test_result->result_smoke) }}'/></a></td>
                  @else
                      <td></td>
                  @endif
              @else
                  <td></td>
              @endif
              @if($item->test_result != null)
                  @if($item->test_result->result_uta != null)
                      <td><a href="{{ 'http://ci.mars.changbaops.com/job/' . $item->test_result->utaresult_job . '/' . $item->test_result->utaresult_id }}"><img src='{{ asset($item->test_result->result_uta) }}'/></a></td>
                  @else
                      <td></td>
                  @endif
              @else
                  <td></td>
              @endif
             <td><a href="{{ '/auto/detail/' . $item->id }}"><botton  class = "btn btn-primary">上线</botton></a></td>
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
@section('js')
@stop