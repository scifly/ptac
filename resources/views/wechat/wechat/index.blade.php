@extends('layouts.wap')
@section('title') {!! config('app.name') !!} @endsection
@section('content')
    <header class="wechat-header">
        <h1 class="wechat-title">{!! config('app.name') !!}</h1>
        <p class='wechat-sub-title'>智慧校园</p>
    </header>
    <div class="weui-cells">4333333</div>
    <div class="weui-grids">
        @foreach ($modules as $module)
            <a href="/{!! !empty($module->uri) ? $module->uri : '#' !!}" class="weui-grid js_grid">
                <div class="weui-grid__icon">
                    <img src="/{!!$module->media->path !!}" alt="">
                </div>
                <p class="weui-grid__label">
                    {!! $module->name !!}
                </p>
            </a>
        @endforeach
    </div>
@endsection