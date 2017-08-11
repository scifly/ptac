@extends('layouts.master')
@section('header')
    <h1>审批详情</h1>
@endsection
@section('content')
    @foreach ($data as $val)
        <p>此流程名为 {{ $val['name'] }}</p>
        <p>此流程状态为 {{ $val['status'] }}</p>
        <hr/>
    @endforeach
@endsection