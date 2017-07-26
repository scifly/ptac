@extends('layouts.master')
@section('header')
    <a href="{{ url('/companies/index') }}">Back to overview</a>
    <h2>
        {{ $company->name }}
    </h2>
    <a href="{{ url('companies/' . 'edit/' . $company->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('companies/' . 'delete/' . $company->id) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $company->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>备注：{{ $company->remark }}</p>
    <p>企业号ID：{{ $company->corpid }}</p>
@endsection