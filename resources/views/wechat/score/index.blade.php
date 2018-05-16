@extends('wechat.layouts.master')
@section('title')
    <title>成绩中心</title>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/wechat/score/index.css') }}">
@endsection
@section('content')
    {{ $content }}
@endsection
@section('script')
    <script src="{{ asset('/js/wechat/score/index.js') }}">
@endsection