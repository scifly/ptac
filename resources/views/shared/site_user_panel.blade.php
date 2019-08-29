<div class="user-panel">
    <div class="pull-left image">
        <img src="{!! Auth::user()->{'avatar_url'} ?? asset('img/user2-160x160.jpg') !!}"
             class="img-circle" alt="User Image">
    </div>
    <div class="pull-left info">
        <p>{!! Auth::user()->{'realname'} !!}</p>
        <a href="#">
            <i class="fa fa-circle text-success"></i>
            {!! Auth::user()->{'group'}->name ?? null !!}
        </a>
    </div>
</div>