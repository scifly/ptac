<aside class="main-sidebar">
    <section class="sidebar">
        @include('partials.site_user_panel')
        @include('partials.site_search')
        <!--左侧菜单-->
        <ul class="sidebar-menu" data-widget="tree">
            {!! $menu !!}
        </ul>
    </section>
</aside>