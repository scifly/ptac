@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $company->name }}
    </h2>
    <a href="{{ url('companies/' . $company->id . '/edit') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('companies/' . $company->id . '/delete') }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $company->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>备注：{{ $company->remark }}</p>
    <p>企业号ID：{{ $company->corpid }}</p>
@endsection