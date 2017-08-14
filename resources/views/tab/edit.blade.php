@extends('layouts.master')
@section('header')
    <h1>编辑卡片</h1>
@endsection
@section('content')
    {!! Form::model($tab, ['method' => 'put', 'id' => 'formTab']) !!}
    @include('tab.create_edit')
    {!! Form::close() !!}
@endsection