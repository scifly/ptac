@extends('layouts.master')
@section('header')
    <h2>编辑考勤机记录</h2>
@endsection
@section('content')
    {!! Form::model($am, ['url' => 'attendance_machines/update' . $am->id, 'method' => 'put', 'id' => 'formAttendanceMachine']) !!}
    @include('attendance_machine.create_edit')
    {!! Form::close() !!}
@endsection