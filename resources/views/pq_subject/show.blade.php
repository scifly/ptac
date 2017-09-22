@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $pqSubject->Subject }}
    </h2>
    <a href="{{ url('pq_subjects/edit/' . $pqSubject->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('pq_subjects/delete/' . $pqSubject->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $pqSubject->updated_at->diffForHumans() }}</p>
@endsection
@section('content')


    <dl class="dl-horizontal">
        <dt>所属学校：</dt>
        <dd>{{ $pqSubject->pollQuestionnaire->name }}</dd>
        <br/>
        <dt>题目类型：</dt>
        <dd>{{ $pqSubject->subject_type }}</dd>
        <br/>
        @foreach($pqSubject->pollquestionnairechoice as $value)
            <dt>选项：</dt>
            <dd>{{ $value->choice }}</dd>
        @endforeach
    </dl>
@endsection