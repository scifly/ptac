@extends('layouts.wap')
@section('title') {!! config('app.name') !!} @endsection
@section('content')
    @if (session('schools') || session('part'))
        <div class="weui-cells weui-cells_form" style="margin-top: 0;">
            <div class="weui-cell">
                <div class="weui-cell__hd" style="text-align: left;">
                    <a href="#" id="show-choices">
                        <img alt="" src="{!! asset("img/nav.png") !!}" style="width: 16px;"/>
                    </a>
                </div>
            </div>
        </div>
    @endif
    <header class="wechat-header">
        <h1 class="wechat-title">{!! config('app.name') !!}</h1>
        <p class='wechat-sub-title'>{!! $school . ($part ? '(' . $part . ')' : '') !!}</p>
        {!! Form::hidden('choices', $choice, ['id' => 'choice']) !!}
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
@section('script')
    <script src="{!! asset('/js/wechat/index.js') !!}"></script>
@endsection