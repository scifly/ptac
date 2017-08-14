@extends('layouts.master')
@section('header')
    <h2>编辑Icon</h2>
@endsection
@section('content')
    {!! Form::model($icon, [
        'method' => 'put',
        'id' => 'formIcon',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('icon.create_edit')
    {!! Form::close() !!}
@endsection