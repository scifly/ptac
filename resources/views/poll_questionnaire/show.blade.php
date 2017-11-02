@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $pollQuestionnaire->name }}
    </h2>
    <a href="{{ url('poll_qusetionnaires/edit/' . $pollQuestionnaire->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('poll_qusetionnaires/delete/' . $pollQuestionnaire->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $pollQuestionnaire->updated_at->diffForHumans() }}</p>
@endsection
@section('content')


    <dl class="dl-horizontal">
        <dt>所属学校：</dt>
        <dd>{{ $pollQuestionnaire->school->name }}</dd>
    </dl>
@endsection