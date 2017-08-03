@extends('layouts.master')
@section('header')
    <h1>添加微网站</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formWapSite','data-parsley-validate' => 'true']) !!}
    @include('wap_site.create_edit')
    {!! Form::close() !!}
@endsection