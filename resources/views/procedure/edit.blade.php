@extends('layouts.master')
@section('header')
    <h2>编辑流程记录</h2>
@endsection
@section('content')
    {!! Form::model($procedure, ['url' => 'procedures/update' . $procedure->id, 'method' => 'put', 'id' => 'formAttendanceMachine']) !!}
    @include('procedure.create_edit')
    {!! Form::close() !!}
@endsection