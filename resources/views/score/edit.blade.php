@extends('layouts.master')
@section('header')
    <h2>编辑学校</h2>
@endsection
@section('content')
    {!! Form::model($school, ['url' => '/schools/' . $school->id, 'method' => 'put']) !!}
    @include('configuration.configuration.partials.school')
    {!! Form::close() !!}
@endsection