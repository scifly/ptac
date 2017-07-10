@extends('layouts.master')
@section('header')
    @if (isset($schoolType))
        <a href="{{ url('/') }}">Back to the overview</a>
    @endif
    <h2>
        所有类型为 @if (isset($schoolType)) {{ $schoolType->name }} @endif 的学校
        <a href="{{ url('schools/create') }}" class="btn btn-primary pull-right">
            添加新学校
        </a>
    </h2>
@endsection
@section('content')
    @foreach ($schools as $school)
        <div class="school">
            <a href="{{ url('schools/' . $school->id) }}">
                <strong>{{ $school->name }}</strong> - {{ $school->schoolType->name }}
            </a>
        </div>
    @endforeach
@endsection