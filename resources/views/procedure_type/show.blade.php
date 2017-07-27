@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $pt->name }}
    </h2>
    <a href="{{ url('procedure_types/edit/'. $pt->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('procedure_types/index') }}">
        <span class="glyphicon glyphicon-trash"></span>
        index
    </a>
    {{--<a href="{{ url('procedure_types/delete/'. $pt->id ) }}">--}}
        {{--<span class="glyphicon glyphicon-trash"></span>--}}
        {{--Delete--}}
    {{--</a>--}}
    <p>Last edited: {{ $pt->updated_at->diffForHumans() }}</p>
@endsection
