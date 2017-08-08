@extends('layouts.master')
@section('header')
    <h1>编辑文章</h1>
@endsection
@section('content')
    {!! Form::model($subject, ['method' => 'put', 'id' => 'formWsmArticle']) !!}
    @include('wsm_article.create_edit')
    {!! Form::close() !!}
@endsection