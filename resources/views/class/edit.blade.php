@extends('layouts.master')
@section('header')
    <h2>编辑班级</h2>
@endsection
@section('content')
    {!! Form::model($squad, ['url' => '/classes/' . $squad->id, 'method' => 'put']) !!}
    @include('configuration.configuration.partials.school')
    {!! Form::close() !!}
@endsection