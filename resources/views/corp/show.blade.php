@extends('layouts.master')
@section('header')
    <a href="{{ url('/corps/index') }}">Back to overview</a>
    <h2>
        {{ $corp->name }}
    </h2>
    <a href="{{ url('corps/' . 'edit/' . $corp->id )  }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{  url('corps/' . 'delete/' . $corp->id )  }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $corp->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>所属运营者：{{$corp->company->name}}</p>
@endsection