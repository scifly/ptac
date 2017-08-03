@extends('layouts.master')
@section('header')
    <h2>编辑总成绩</h2>
@endsection
@section('content')
    {!! Form::model($scoreTotal, ['method' => 'put', 'id' => 'formScoreTotal', 'data-parsley-validate' => 'true']) !!}
    @include('score_total.create_edit')
    {!! Form::close() !!}
@endsection