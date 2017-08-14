@extends('layouts.master')
@section('header')
    <h2>编辑Icon类型</h2>
@endsection
@section('content')
    {!! Form::model($action, [
        'method' => 'put',
        'id' => 'formIconType',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('icon_type.create_edit')
    {!! Form::close() !!}
@endsection