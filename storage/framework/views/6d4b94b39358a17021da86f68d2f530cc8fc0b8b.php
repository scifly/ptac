<aside class="main-sidebar">
    <section class="sidebar">
        <?php echo $__env->make('partials.site_user_panel', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php echo $__env->make('partials.site_search', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <!--左侧菜单-->
        <ul class="sidebar-menu" data-widget="tree">
            <?php echo $menu; ?>

        </ul>
    </section>
</aside>