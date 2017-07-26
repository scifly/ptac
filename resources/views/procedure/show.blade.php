@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $procedure->name }}
    </h2>
    <a href="{{ url('procedures/edit/'. $procedure->id) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('procedures/delete/'. $procedure->id) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $procedure->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>备注：{{ $procedure->remark }}</p>
    <p>
        @if ($procedure->procedure_type_id)
            流程类型:
            {{ link_to('procedures/procedureType/' . $procedure->procedureType->name, $procedure->procedureType->name) }}
        @endif
    </p>
    <p>
        @if ($procedure->school_id)
            所属学校:
            {{ link_to('procedures/school/' . $procedure->school->name, $procedure->school->name) }}
        @endif
    </p>
@endsection