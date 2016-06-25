@extends('common.main')

@section('css')
   <link rel="stylesheet" type="text/css" href="{{ asset('/static/css/item-job.css') }}">
@stop

@section('js')
    <script type="text/javascript" src="{{ asset('static/js/deployaction.js') }}"></script>
@stop

@section('content')
  <div class="container">

     <div class="title">部署详情</div>
     <div class="first-line">
        <div class="left">
          <table class="table table-bordered table-striped">
             <thead>
                <tr>
                   <th>Metadata</th>
                   <th></th>
                </tr>
             </thead>
             <tbody>
                <tr>
                   <td>产品</td>
                   <td>{{ $item->productid }}</td>
                </tr>
                <tr>
                   <td>版本号</td>
                   <td>{{ $item->buildid }}</td>
                </tr>
                <tr>
                   <td>SCM地址</td>
                   <td>{{ $item->svnhost }}</td>
                </tr>
                <tr>
                   <td>SCM版本</td>
                   <td>{{ $item->svnrevision }}</td>
                </tr>
                <tr>
                   <td>checksum</td>
                   <td>{{ $item->checksum }}</td>
                </tr>
                <tr>
                   <td>版本库地址</td>
                   <td>{{ $item->artifactoryhost }}</td>
                </tr>
                <tr>
                   <td>版本库路径</td>
                   <td>{{ $item->artifactorydir }}</td>
                </tr>
                <tr>
                   <td>存档时间</td>
                   <td>{{ $item->archivetime }}</td>
                </tr>
                <tr>
                   <td>状态</td>
                   <td>{{ $item->status }}</td>
                </tr>
                <tr>
                   <td>部署时间</td>
                    @if( $item->result_history != null)
                        @foreach($item->result_history as $history)
                            <td>{{ $history->lastupdate }}</td>
                        @endforeach
                    @else
                        <td></td>
                    @endif
                </tr>
             </tbody>
             <tbody>
             	<tr>
             		<td>主机名:</td>
             		<td><textarea  class="hostsname form-control" name="" guid="{{ $item->id }}">@if($hostinfo != null){{ $hostinfo->host }}@endif</textarea></td>
             	</tr>
             	<tr>
             		<td >targetdirectory:</td>
             		<td><input  class="targetdirectory form-control" name="" @if($hostinfo != null)value="{{ $hostinfo->targetdir }}"@endif></td>
             	</tr>
         	</tbody>
          </table>
        </div>
        <div class="center">
            <div class="svnmessage">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>guid</th>
                        <th>buildid</th>
                        <th>部署时间</th>
                        <th>上一个svn版本</th>
                    </tr>
                    </thead>
                    <tbody class="result-list">
                        <tr>
                            @if($lastrelease != null)
                                <td>{{ $lastrelease->id }}</td>
                                <td>{{ $lastrelease->buildid }}</td>
                                @if($lastupdate != null)
                                    <td>{{ $lastupdate->lastupdate }}</td>
                                @else
                                    <td></td>
                                @endif
                                <td>{{ $lastrelease->svnrevision }}</td>
                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="distinguish"><img src="{{ asset('/static/img/loading.gif') }}" width="100%"/></div>
        </div>
     </div>

     <div class="second-line">
        <div class="test-result ">
            <table class="table table-bordered table-striped table-hover ">
                <thead>
                    <tr>
                        <th></th>
                        <th>结果</th>
                        <th>Total</th>
                        <th>Pass</th>
                        <th>fail</th>
                        <th>skip</th>
                    </tr>
                </thead>
                <tbody class="result-list">
                    <tr>
                        <td>Smoke</td>
                        @if($item->test_result != null)
                            @if($item->test_result->result_smoke != null)
                                <td><a href="{{ 'http://ci.mars.changbaops.com/job/' . $item->test_result->smokeresult_job . '/' . $item->test_result->smokeresult_id }}"><img src='{{ asset($item->test_result->result_smoke) }}'/></a></td>
                            @else
                                <td></td>
                            @endif
                        @else
                            <td></td>
                        @endif
                        @if($smoke != null)
                            <td>{{ $smoke['totalCount'] }}</td>
                            <td>{{ $smoke['totalCount']- $smoke['failCount'] - $smoke['skipCount']}}</td>
                            <td>{{ $smoke['failCount'] }}</td>
                            <td>{{ $smoke['skipCount'] }}</td>
                        @else
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif
                    </tr>
                    <tr>
                        <td>Uat</td>
                        @if($item->test_result != null)
                            @if($item->test_result->result_uta != null)
                                <td><a href="{{ 'http://ci.mars.changbaops.com/job/' . $item->test_result->utaresult_job . '/' . $item->test_result->utaresult_id }}"><img src='{{ asset($item->test_result->result_uta) }}'/></a></td>
                            @else
                                <td></td>
                            @endif
                        @else
                            <td></td>
                        @endif
                        @if($uat != null)
                            <td>{{ $uat['totalCount'] }}</td>
                            <td>{{ $uat['totalCount']- $uat['failCount'] - $uat['skipCount']}}</td>
                            <td>{{ $uat['failCount'] }}</td>
                            <td>{{ $uat['skipCount'] }}</td>
                        @else
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
     </div>
     <div class="third-line">
        <button class="deploy">部署</button>
        <div class="deploy-process">
        	<table class="table table-bordered table-striped ">
			<thead>
				<tr>
                    <th>部署主机</th>
                    <th>文件分发&Checksum</th>
				    <th>API校验－Host</th>
				    <th>API校验－线上</th>
				</tr>
			</thead>
				<tbody class="resultpic">
					<tr>
                        <td class="result-host">未开始</td>
                        <td class="result-pic1">未开始</td>
					    <td class="result-pic2">未开始</td>
					    <td class="result-pic3">未开始</td>
					</tr>
				</tbody>
			</table>
        </div>
     </div>

  </div>
@stop