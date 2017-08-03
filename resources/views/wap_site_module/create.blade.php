@extends('layouts.master')
@section('header')
    <h1>添加网站模块</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formWapSiteModule','data-parsley-validate' => 'true']) !!}
    @include('wap_site_module.create_edit')
    {!! Form::close() !!}
@endsection