@extends('layouts.master')
@section('header')
    <h2>编辑学校</h2>
@endsection
@section('content')
    {!! Form::model($school, ['url' => '/schools/' . $school->id, 'method' => 'put', 'id' => 'formSchool']) !!}
    @include('school.create_edit')
    {!! Form::close() !!}
@endsection