@extends('layouts.master')
@section('header')
    <h1>添加新考勤机</h1>
@endsection
@section('content')
    {!! Form::open([
        'method' => 'post',
        'id' => 'formAttendanceMachine',
        'data-parsley-validate' => 'true'
    ]) !!}
    @include('attendance_machine.create_edit')
    {!! Form::close() !!}
@endsection