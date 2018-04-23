<section class="content-header">
    @if (isset($enabled))
        <h1>
            <i class="fa {!! $department['icon'] !!}">
                <b>{!! $department['name']!!}</b>
            </i>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
            <li class="active">Dashboard</li>
        </ol>
    @endif
</section>