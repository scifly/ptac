@extends('layouts.wap')
@section('title')
    <title>微信h5支付测试</title>
@endsection
@section('content')
    {!! Form::open(['method' => 'post', 'id' => 'formTest']) !!}
    {!! Form::button('pay') !!}
    {!! Form::close() !!}
@endsection