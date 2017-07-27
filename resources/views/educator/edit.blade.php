@extends('layouts.master')
@section('header')
    <h2>编辑教职员工</h2>
@endsection
@section('content')
    {!! Form::model($educator, [ 'method' => 'put', 'id' => 'fromEducator']) !!}
    @include('configuration.configuration.partials.school')
    {!! Form::close() !!}
@endsection