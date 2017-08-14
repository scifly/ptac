@extends('layouts.master')
@section('header')
    <h2>编辑班级</h2>
@endsection
@section('content')
    {!! Form::model($squad, [ 'method' => 'put', 'id' => 'formSquad', 'data-parsley-validate' => 'true']) !!}
    @include('class.create_edit')
    {!! Form::close() !!}
@endsection