@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $grade->name }}
    </h2>
    <a href="{{ url('grades/' . $grade->id . '/edit') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('grades/' . $grade->id . '/delete') }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $grade->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>地址：{{ $grade->address }}</p>
    <p>
        @if ($grade->schoolType)
            类型:
            {{ link_to('schools/schoolTypes/' . $school->schoolType->name, $school->schoolType->name) }}
        @endif
    </p>
@endsection