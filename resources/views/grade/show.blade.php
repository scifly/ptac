@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $grade->name }}
    </h2>
    <a href="{{ url('grades/edit/' . $grade->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('grades/delete/' . $grade->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $grade->updated_at->diffForHumans() }}</p>
@endsection
@section('content')


    <dl class="dl-horizontal">
        <dt>所属学校：</dt><dd>{{ $grade->school->name }}</dd>
        <br/>
        <dt>教职员工组：</dt>
        @foreach($educators as $v)
            <dd>{{ $v['username'] }}</dd>
            @endforeach
    </dl>
@endsection