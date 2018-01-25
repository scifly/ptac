<header class="main-header">
    <!-- Logo -->
    <?php echo $__env->make('partials.site_logo', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!--提醒-->
                <li class="dropdown notifications-menu">
                    <?php echo $__env->make('partials.site_notification', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </li>
                <!--任务-->
                <li class="dropdown tasks-menu">
                    <?php echo $__env->make('partials.site_task', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </li>
                <!--用户账号-->
                <li class="dropdown user user-menu">
                    <?php echo $__env->make('partials.site_user', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </li>
            </ul>
        </div>
    </nav>
</header>