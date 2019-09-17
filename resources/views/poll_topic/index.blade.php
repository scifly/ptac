@extends('layouts.master')
@section('header')

@endsection
@section('breadcrumb')
    发起/参与/查询统计
@endsection
@section('content')
    {!! Form::open(['route' => 'pqp_update', 'method' => 'put']) !!}

    请选择问卷调查:
    <select name="pollQuestion">
        <option value="0"><--请选择问卷调查--></option>
        @foreach($pqs as $pq){
        <option value="{{$pq->id}}">{{$pq->name}}</option>
        }
        @endforeach
    </select>
    <div class="btn-group" name="Submit">
        <button type="submit" class="btn btn-primary pull-right">
            提交
        </button>
    </div>
    <div class="panel panel-default" id="panel">
    </div>
    <div class="btn-group" name="Submit">
        <button type="submit" class="btn btn-primary pull-right">
            提交
        </button>
    </div>
    {!! Form::close() !!}
@endsection

