<input type="hidden" id="userId" value="{{ Auth::id() }}"/>
<a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <img src="{{ Auth::user()->avatar_url ?? URL::asset('img/default.png') }}"
         class="user-image" alt="用户头像">
    <span class="hidden-xs">{{ Auth::user()->realname }}</span>
</a>
<ul class="dropdown-menu">
    <li class="user-header">
        <img src="{{ Auth::user()->avatar_url ?? URL::asset('img/default.png') }}"
             class="img-circle" alt="用户头像">
        <p>
            {{ Auth::user()->realname }}
            <small>角色：{{ Auth::user()->group->name ?? null }}</small>
        </p>
    </li>
    <li class="user-footer">
        <div class="pull-left">
            <a href="#" class="btn btn-default btn-flat btn-sm" id="profile">
                <i class="fa fa-user"> 个人信息</i>
            </a>
        </div>
        <div class="pull-right">
            <a href="{{ URL::route('logout') }}" class="btn btn-default btn-flat btn-sm">
                <i class="fa fa-sign-out"> 退出</i>
            </a>
        </div>
    </li>
</ul>