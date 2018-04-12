<header class="main-header">
    <!-- Logo -->
    @include('partials.site_logo')
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown messages-menu open">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                        <i class="fa fa-send-o" style="margin-right: 15px;"></i>四川盛世华唐
                    </a>
                </li>
                <!--用户账号-->
                <li class="dropdown user user-menu">
                    @include('partials.site_user')
                </li>
                <!-- 提醒 -->
                <li class="dropdown notifications-menu">
                    @include('partials.site_notification')
                </li>
                <!--任务-->
                <li class="dropdown tasks-menu">
                    @include('partials.site_task')
                </li>
            </ul>
        </div>
    </nav>
</header>