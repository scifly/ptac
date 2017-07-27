@extends('layouts.master')
@section('header')
    <h2>编辑考勤机记录</h2>
@endsection
@section('content')
    {!! Form::model($am, ['method' => 'put', 'id' => 'formAttendanceMachine', 'data-parsley-validate' => 'true']) !!}
    @include('attendance_machine.create_edit')
    {!! Form::close() !!}
@endsection