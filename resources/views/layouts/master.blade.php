<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf_token" content="{{ csrf_token() }}" id="csrf_token">
    <title>{!! config('app.name') !!}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/gritter/css/jquery.gritter.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/icheck/all.css') }}">
    @isset($jstree)
        <link rel="stylesheet" href="{{ URL::asset('js/plugins/jstree/dist/themes/default/style.min.css') }}">
    @endisset
    @isset($map)
        <link rel="stylesheet" href="{{ URL::asset('css/jquery-jvectormap.css') }}">
    @endisset
    @isset($form)
        <link rel="stylesheet" href="{{ URL::asset('js/plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('js/plugins/parsley/parsley.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('js/plugins/switchery/switchery.min.css') }}">
    @endisset
    @isset($datatable)
        <link rel="stylesheet" href="{{ URL::asset('js/plugins/datatables/datatables.min.css') }}">
    @endisset
    @isset($ws)
        {{--<link rel="stylesheet" href="{{ URL::asset('Css/wapSite.Css') }}">--}}
    @endisset
    @isset($fullcalendar)
        <link rel="stylesheet" href="{{ URL::asset('js/plugins/fullcalendar/css/fullcalendar.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('js/plugins/fullcalendar/css/jquery-ui.min.css') }}">
    @endisset
    {{--审核详情--}}
    <link rel="stylesheet" href="{{ URL::asset('css/procedure_info.css') }}">
    <!-- fileinput-->
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/fileinput/css/fileinput.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/fileinput/themes/explorer/theme.css') }}">
    {{--图片上传--}}
    <link rel="stylesheet" href="{{ URL::asset('css/imgInput.css') }}">
    <!--微网站应用菜单管理-->
    <link rel="stylesheet" href="{{ URL::asset('css/showEditMenu.css') }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Google Font -->

    {{--<link rel="stylesheet" href="{{ URL::asset('css/google_font.css') }}">--}}
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <a href="#" class="logo">
            <span class="logo-mini"><b>A</b>LT</span>
            <span class="logo-lg"><b>Admin</b>LTE</span>
        </a>
        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!--提醒-->
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning">10</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">您有10条新提醒！</li>
                            <li>
                                <ul class="menu">
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-users text-aqua"></i> 今天有5个新会员加入！
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-user text-red"></i> 你修改了用户名！
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="footer"><a href="#">查看所有</a></li>
                        </ul>
                    </li>
                    <!--任务-->
                    <li class="dropdown tasks-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-flag-o"></i>
                            <span class="label label-danger">9</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">您有9项任务！</li>
                            <li>
                                <ul class="menu">
                                    <li>
                                        <a href="#">
                                            <h3>
                                                设计一些按钮
                                                <small class="pull-right">20%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-aqua" style="width: 20%"
                                                     role="progressbar"
                                                     aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">20% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <h3>
                                                新建一个主题
                                                <small class="pull-right">40%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: 40%"
                                                     role="progressbar"
                                                     aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                    <span class="sr-only">40% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="footer">
                                <a href="#">查看所有任务</a>
                            </li>
                        </ul>
                    </li>
                    <!--用户账号-->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ asset('img/user2-160x160.jpg') }}" class="user-image" alt="User Image">
                            <span class="hidden-xs">张三</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-header">
                                <img src="{{ asset('img/user2-160x160.jpg') }}" class="img-circle"
                                     alt="User Image">

                                <p>
                                    张三 - 程序员
                                    <small>2012年入会</small>
                                </p>
                            </li>
                            <li class="user-body">
                                <div class="row">
                                    <div class="col-xs-4 text-center">
                                        <a href="#">关注</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">朋友</a>
                                    </div>
                                </div>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="#" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="#" class="btn btn-default btn-flat">退出登录</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!--右边设置面板-->
                    <li>
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!--左侧面板-->
    <aside class="main-sidebar">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{ asset('img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p>张三</p>
                    <a href="#"><i class="fa fa-circle text-success"></i> 技术部</a>
                </div>
            </div>
            <!-- search form -->
            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="搜索...">
                    <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                    <i class="fa fa-search"></i>
                </button>
              </span>
                </div>
            </form>
            <!--左侧菜单-->
            <ul class="sidebar-menu" data-widget="tree">
                @yield('menu')
            </ul>
        </section>
    </aside>
    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content-header">
            <h1>@yield('header')</h1>
            <ol class="breadcrumb">@yield('breadcrumb')</ol>
        </section>
        <!--content-->
        <section class="content">
            @isset($dialog)
                @include('partials.modal_delete')
            @endif
            @yield('content')
        </section>
    </div>
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 2.4.0
        </div>
        <strong>Copyright &copy; 2014-2016 <a href="#">Almsaeed Studio</a>.</strong> All rights
        reserved.
    </footer>
    <!--右侧设置面板-->
    <aside class="control-sidebar control-sidebar-dark">
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
            <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
            <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" id="control-sidebar-home-tab">
                <h3 class="control-sidebar-heading">近期活动</h3>
                <ul class="control-sidebar-menu">
                    <li>
                        <a href="javascript:void(0)">
                            <i class="menu-icon fa fa-birthday-cake bg-red"></i>

                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                                <p>Will be 23 on April 24th</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <i class="menu-icon fa fa-user bg-yellow"></i>

                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                                <p>New phone +1(800)555-1234</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                                <p>nora@example.com</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <i class="menu-icon fa fa-file-code-o bg-green"></i>

                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                                <p>Execution time 5 seconds</p>
                            </div>
                        </a>
                    </li>
                </ul>
                <h3 class="control-sidebar-heading">任务进程</h3>
                <ul class="control-sidebar-menu">
                    <li>
                        <a href="javascript:void(0)">
                            <h4 class="control-sidebar-subheading">
                                Custom Template Design
                                <span class="label label-danger pull-right">70%</span>
                            </h4>

                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <h4 class="control-sidebar-subheading">
                                Update Resume
                                <span class="label label-success pull-right">95%</span>
                            </h4>

                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-success" style="width: 95%"></div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <h4 class="control-sidebar-subheading">
                                Laravel Integration
                                <span class="label label-warning pull-right">50%</span>
                            </h4>

                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">
                            <h4 class="control-sidebar-subheading">
                                Back End Framework
                                <span class="label label-primary pull-right">68%</span>
                            </h4>

                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-pane" id="control-sidebar-settings-tab">
                <form method="post">
                    <h3 class="control-sidebar-heading">通用设置</h3>

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Report panel usage
                            <input type="checkbox" class="pull-right" checked>
                        </label>

                        <p>
                            Some information about this general settings option
                        </p>
                    </div>
                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Allow mail redirect
                            <input type="checkbox" class="pull-right" checked>
                        </label>

                        <p>
                            Other sets of options are available
                        </p>
                    </div>
                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Expose author name in posts
                            <input type="checkbox" class="pull-right" checked>
                        </label>

                        <p>
                            Allow the user to show his name in blog posts
                        </p>
                    </div>
                    <h3 class="control-sidebar-heading">聊天设置</h3>

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Show me as online
                            <input type="checkbox" class="pull-right" checked>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Turn off notifications
                            <input type="checkbox" class="pull-right">
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Delete chat history
                            <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
                        </label>
                    </div>
                </form>
            </div>
        </div>
    </aside>
    <!-- 添加侧边栏的背景。 这个div必须紧接着侧边栏 -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- jQuery 3 / Bootstrap 3.3.7 / AdminLTE App / Gritter / Admin.CRUD / Demo -->
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('js/adminlte.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/gritter/js/jquery.gritter.js') }}"></script>
<script src="{{ URL::asset('js/plugins/icheck/icheck.min.js') }}"></script>
@isset($jstree)
    <script src="{{ URL::asset('js/plugins/jstree/dist/jstree.min.js') }}"></script>
@endisset
@isset($form)
    <!-- Parsley / Select2 / Switchery -->
    <script src="{{ URL::asset('js/plugins/parsley/parsley.min.js') }}"></script>
    <script src="{{ URL::asset('js/plugins/parsley/i18n/zh_cn.js') }}"></script>
    <script src="{{ URL::asset('js/plugins/parsley/i18n/zh_cn.extra.js') }}"></script>
    <script src="{{ URL::asset('js/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('js/plugins/switchery/switchery.min.js') }}"></script>
    <script src="{{ URL::asset('js/switcher.init.js') }}"></script>
@endisset
@isset($datatable)
    <script src="{{ URL::asset('js/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('js/plugins/datatables/dataTables.checkboxes.min.js') }}"></script>
@endisset
@isset($map)
    <script src="{{ URL::asset('js/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ URL::asset('Scripts') }}"></script>
@endisset
@isset($chart)
    <script src="{{ URL::asset('js/Chart.min.js') }}"></script>
@endisset
@isset($fullcalendar)
    <script src="{{ URL::asset('js/plugins/fullcalendar/js/jquery-ui.min.js') }}"></script>
    <script src="{{ URL::asset('js/plugins/fullcalendar/js/jquery-ui-timepicker-addon.js') }}"></script>
    <script src="{{ URL::asset('js/plugins/fullcalendar/js/moment.min.js') }}"></script>
    <script src="{{ URL::asset('js/plugins/fullcalendar/js/fullcalendar.min.js') }}"></script>
@endisset
<script src="{{ URL::asset('js/demo.js') }}"></script>
@isset($js)
    <script src="{{ URL::asset($js) }}"></script>
@endisset

{{--echarts--}}
<script src="{{ URL::asset('js/plugins/echarts/echarts.common.min.js') }}"></script>
<!-- fileinput-->
<script src="{{ URL::asset('js/plugins/fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/fileinput/js/locales/zh.js') }}"></script>
<script src="{{ URL::asset('js/plugins/fileinput/themes/explorer/theme.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ URL::asset('js/demo.js') }}"></script>
</body>
</html>
