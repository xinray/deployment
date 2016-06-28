<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">导航条</li>
            <!-- Optionally, you can add icons to the links -->
            <li class="treeview @if(stripos(request()->path(), '/auto/dashboard') !== false) active @endif">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li @if(stripos(request()->path(), '/auto/dashboard') !== false) class="active" @endif>
                        <a href="{{ url('/auto/dashboard') }}"><i class="fa fa-circle-o"></i>所有产品 </a>
                    </li>
                    @foreach(config('data')['product'] as $v)
                        <li @if(stripos(request()->path(), '/auto/dashboard' . $v['productid'] .'&num=50') !== false) class="active" @endif>
                            <a href="{{ url('/auto/dashboard') }}"><i class="fa fa-circle-o"></i> {{ $v['productid'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li class="treeview @if(stripos(request()->path(), 'item') !== false) active @endif">
                <a href="#">
                    <i class="fa fa-list-ul"></i> <span>版本库</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li @if(stripos(request()->path(), '/item/list') !== false) class="active" @endif>
                        <a href="{{ url('/item/list?num=50') }}"><i class="fa fa-circle-o"></i>所有产品 </a>
                    </li>
                    @foreach(config('data')['product'] as $v)
                        <li @if(stripos(request()->path(), '/item/list?type=' . $v['productid'] .'&num=50') !== false) class="active" @endif>
                            <a href="{{ url('/item/list?type=' . $v['productid'] .'&num=50') }}"><i class="fa fa-circle-o"></i> {{ $v['productid'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li class="treeview @if(stripos(request()->path(), '/auto/history/reslult') !== false) active @endif">
                <a href="#">
                    <i class="fa fa-history"></i> <span>部署历史</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    @foreach(config('data')['product'] as $v)
                        <li @if(stripos(request()->path(), '/auto/history/reslult?type=' . $v['productid'] .'&num=50') !== false) class="active" @endif>
                            <a href="{{ url('/auto/history/reslult?type=' . $v['productid'] .'&num=50') }}"><i class="fa fa-circle-o"></i> {{ $v['productid'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>

        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>