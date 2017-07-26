@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $pt->name }}
    </h2>
    <a href="{{ url('procedure_types/' . $pt->id . '/edit') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('procedure_types/' . $pt->id . '/delete') }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $pt->updated_at->diffForHumans() }}</p>
@endsection
