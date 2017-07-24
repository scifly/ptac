@extends('layouts.master')
@section('header')
    <h2>编辑年级</h2>
@endsection
@section('content')
    {!! Form::model($grade, ['url' => '/grades/' . $grade->id, 'method' => 'put']) !!}
    @include('configuration.configuration.partials.school')
    {!! Form::close() !!}
@endsection