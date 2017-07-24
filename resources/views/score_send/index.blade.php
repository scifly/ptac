@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('scores/create') }}" class="btn btn-primary pull-right">
                成绩预览
            </a>
        </div>
        <div class="btn-group">
            <a href="{{ url('scores/create') }}" class="btn btn-primary pull-right">
                成绩发送
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    成绩管理/成绩发送
@endsection
@section('content')

    学校:
    <select name="school" id="school">
        <option value="0"><--请选择学校--></option>
        @if(isset($schools))
            @foreach($schools as $school)
            <option value="{{$school->id}}">{{$school->name}}</option>
            @endforeach
        @endif
    </select>

    考次:
    <select name="exam" >
        <option value="0"><--请选择考次--></option>
    </select>

    年级:
    <select name="grade" >
    </select>
    班级:
    <select name="class" >
    </select>
@endsection