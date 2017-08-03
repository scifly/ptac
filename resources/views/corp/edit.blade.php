@extends('layouts.master')
@section('header')
    <h2>编辑企业</h2>
@endsection
@section('content')
    {!! Form::model($corp, ['method' => 'put', 'id' => 'formCorp', 'data-parsley-validate' => 'true']) !!}
    @include('corp.create_edit')
    {!! Form::close() !!}
@endsection