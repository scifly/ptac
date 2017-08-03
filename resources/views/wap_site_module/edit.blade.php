@extends('layouts.master')
@section('header')
    <h1>编辑网站模块</h1>
@endsection
@section('content')
    {!! Form::model($wapSiteModule, ['method' => 'put', 'id' => 'formWapSiteModule']) !!}
    @include('wap_site_module.create_edit')
    {!! Form::close() !!}
@endsection