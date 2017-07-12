@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $school->name }}
    </h2>
    <a href="{{ url('schools/' . $school->id . '/edit') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('schools/' . $school->id . '/delete') }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $school->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>地址：{{ $school->address }}</p>
    <p>
        @if ($school->schoolType)
            类型:
            {{ link_to('schools/schoolTypes/' . $school->schoolType->name, $school->schoolType->name) }}
        @endif
    </p>
@endsection