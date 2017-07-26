@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $subject->name }}
    </h2>
    <a href="{{ url('subject/' . $subject->id . '/edit') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('subject/' . $subject->id . '/delete') }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $subject->updated_at->diffForHumans() }}</p>
@endsection
