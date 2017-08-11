@extends('layouts.master')
@section('header')
    <h1>审批详情</h1>
@endsection
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="stepflex">
                @foreach ($data as $key => $val)
                    <dl class="@if ($val['status']== 0)success @elseif ($val['status']== 1)fail @else doing @endif">
                        <dt class="s-num">{{ $key+1 }}</dt>
                        <dd class="s-text">{{ $val['name'] }}</dd>
                    </dl>
                @endforeach
            </div>
        </div>
    </div>
@endsection