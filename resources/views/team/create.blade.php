@extends('layouts.master')
@section('header')
    <h1>添加新教职员工组</h1>
@endsection
@section('content')
    {!! Form::open([
        'method' => 'post',
        'id' => 'formTeam',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('team.create_edit')
    {!! Form::close() !!}
@endsection