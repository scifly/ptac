<input type="hidden" id="userId" value="{{ Auth::id() }}"/>
<a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <img src="{{ URL::asset('img/user2-160x160.jpg') }}" class="user-image" alt="用户头像">
    <span class="hidden-xs">{{ Auth::user()->realname }}</span>
</a>
<ul class="dropdown-menu">
    <li class="user-header">
        <img src="{{ URL::asset('img/user2-160x160.jpg') }}" class="img-circle" alt="用户头像">
        <p>
            {{ Auth::user()->realname }} - {{ Auth::user()->group->name ?? null }}
            <small>2012年入会</small>
        </p>
    </li>
    <li class="user-footer">
        <div class="pull-left">
            <a href="#" class="btn btn-default btn-flat" id="profile">个人信息</a>
        </div>
        <div class="pull-right">
            <a href="{{ URL::route('logout') }}" class="btn btn-default btn-flat">退出登录</a>
        </div>
    </li>
</ul>