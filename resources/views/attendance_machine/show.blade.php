@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $am->name }}
    </h2>
    <a href="{{ url('attendance_machines/edit/'. $am->id) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('attendance_machines/index') }}">
        <span class="glyphicon glyphicon-edit"></span>
        index
    </a>
    {{--<a href="{{ url('attendance_machines/delete/'. $am->id) }}">--}}
        {{--<span class="glyphicon glyphicon-trash"></span>--}}
        {{--Delete--}}
    {{--</a>--}}
    <p>Last edited: {{ $am->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>所属位置：{{ $am->location }}</p>
    <p>
        @if ($am->school_id)
            所属学校:
            {{ link_to('schools/show/' . $am->school->id, $am->school->name) }}
        @endif
    </p>
@endsection