@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('score_totals/create') }}" class="btn btn-primary pull-right">
                添加新成绩
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    成绩管理/成绩管理
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>学号</th>
                    <th>姓名</th>
                    <th>考试名称</th>
                    <th>总成绩</th>
                    <th>班级排名</th>
                    <th>年级排名</th>
                    <th>创建时间</th>
                    <th>更新时间</th>
                    <th>状态</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection