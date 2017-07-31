@extends('layouts.master')
@section('header')
    <h1>添加新考勤机</h1>
@endsection
@section('content')
    {!! Form::open(['url' => 'attendance_machines/store', 'method' => 'post', 'id' => 'formAttendanceMachine']) !!}
    @include('attendance_machine.create_edit')
    {!! Form::close() !!}
@endsection