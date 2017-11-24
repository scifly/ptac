<div class="user-panel">
    <div class="pull-left image">
        <img src="{{ URL::asset('img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
    </div>
    <div class="pull-left info">
        <p>{{ $user->realname }}</p>
        <a href="#">
            <i class="fa fa-circle text-success"></i>
            {{ isset($user->group->name) ? $user->group->name : null }}
        </a>
    </div>
</div>