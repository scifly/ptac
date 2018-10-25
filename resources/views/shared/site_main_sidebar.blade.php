<aside class="main-sidebar">
    <section class="sidebar">
        @include('shared.site_user_panel')
        @include('shared.site_search')
        <!--左侧菜单-->
        <ul class="sidebar-menu" data-widget="tree">
            {!! $menu !!}
        </ul>
    </section>
</aside>