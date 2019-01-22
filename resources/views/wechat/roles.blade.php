@extends('layouts.wap')
@section('title') {!! config('app.name') !!} @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/list.css') !!}">
@endsection
@section('content')
    <div class="weui-cells__title">请选择</div>
    <div class="weui-cells" style="margin-top: 0;">
        <a class="weui-cell weui-cell_access" href="?is_educator=1">
            <div class="weui-cell__bd">
                <p>{!! Auth::user()->group->name !!}</p>
            </div>
            <div class="weui-cell__ft"></div>
        </a>
        <a class="weui-cell weui-cell_access" href="?is_educator=0">
            <div class="weui-cell__bd">
                <p>监护人</p>
            </div>
            <div class="weui-cell__ft"></div>
        </a>
    </div>
@endsection