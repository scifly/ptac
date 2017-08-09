@extends('layouts.master')
@section('header')
    <h1>添加文章</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formWsmArticle','data-parsley-validate' => 'true']) !!}
    @include('wsm_article.create_edit')
    {!! Form::close() !!}
@endsection