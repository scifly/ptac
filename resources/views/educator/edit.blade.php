@extends('layouts.master')
@section('header')
    <h2>编辑教职员工</h2>
@endsection
@section('content')
    {!! Form::model($educator, ['url' => '/educators/' . $educator->id, 'method' => 'put']) !!}
    @include('configuration.configuration.partials.school')
    {!! Form::close() !!}
@endsection