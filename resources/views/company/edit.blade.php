@extends('layouts.master')
@section('header')
    <h2>编辑运营者公司</h2>
@endsection
@section('content')
    {!! Form::model($company, ['url' => '/companines/edit' . $company->id, 'method' => 'put']) !!}
    @include('configuration.configuration.partials.school')
    {!! Form::close() !!}
@endsection