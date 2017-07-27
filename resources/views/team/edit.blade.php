@extends('layouts.master')
@section('header')
    <h2>编辑教职员工组</h2>
@endsection
@section('content')
    {!! Form::model($team, [
        'method' => 'put',
        'id' => 'formTeam',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('team.create_edit')
    {!! Form::close() !!}
@endsection