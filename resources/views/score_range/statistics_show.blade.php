@extends('layouts.master')
@section('header')
    <form action="{{ url('statistics') }}" method="post">
        <input type="text" name="type">
        <input type="text" name="id">
        <input type="text" name="exam_id">
        <input type="submit" value="提交">
    </form>
@endsection
