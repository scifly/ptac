@extends('layouts.wap')
@section('title')
    <title>{{ $app }}</title>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/wechat/schools.css') }}">
@endsection
@section('content')
    <div class="weui-cells__title">请选择</div>
    <div class="weui-cells" style="margin-top: 0;">
        @foreach ($schools as $id => $value)
            <a class="weui-cell weui-cell_access" href="{{ $url . $id }}">
                <div class="weui-cell__bd">
                    <p>{{ $value }}</p>
                </div>
                <div class="weui-cell__ft"></div>
            </a>
        @endforeach
    </div>
@endsection