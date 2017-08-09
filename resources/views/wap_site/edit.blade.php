@extends('layouts.master')
@section('header')
    <h1>编辑微网站</h1>
@endsection
@section('content')
    {!! Form::model($wapsite, ['method' => 'put', 'id' => 'formWapSite']) !!}
    @include('wap_site.create_edit')
    {!! Form::close() !!}
@endsection