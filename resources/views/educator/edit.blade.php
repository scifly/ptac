@extends('layouts.master')
@section('header')
    <h2>编辑教职员工</h2>
@endsection
@section('content')
    {!! Form::model($educator, [ 'method' => 'put', 'id' => 'fromEducator']) !!}
    @include('educator.create_edit')
    {!! Form::close() !!}
@endsection