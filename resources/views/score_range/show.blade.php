@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $scoreRange->name }}
    </h2>
    <a href="{{ url('score_ranges/edit/' . $scoreRange->id) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('score_ranges/delete/' . $scoreRange->id) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>所属学校: {{ $scoreRange->school->name }}</p>
    <p>起始分数: {{ $scoreRange->start_score }}</p>
    <p>截止分数: {{ $scoreRange->end_score }}</p>
    <p>统计科目: {{ $scoreRange->subject_ids }}</p>
    <p>Last edited: {{ $scoreRange->updated_at->diffForHumans() }}</p>
@endsection
