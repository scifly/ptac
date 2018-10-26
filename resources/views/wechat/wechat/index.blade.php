@extends('layouts.wap')
@section('title')
    <title>{!! config('app.name') !!}</title>
@endsection
@section('content')
    <header class="wechat-header">
        <h1 class="wechat-title">{!! config('app.name') !!}</h1>
    </header>
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