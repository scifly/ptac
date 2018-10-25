<header class="main-header">
    <!-- Logo -->
    @include('shared.site_logo')
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!--用户账号-->
                <li class="dropdown user user-menu">
                    @include('shared.site_user')
                </li>
                <!-- 提醒 -->
                <li class="dropdown notifications-menu">
                    @include('shared.site_notification')
                </li>
                <!--任务-->
                <li class="dropdown tasks-menu">
                    @include('shared.site_task')
                </li>
            </ul>
        </div>
    </nav>
</header>