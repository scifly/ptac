@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('score_ranges/create') }}" class="btn btn-primary pull-right">
                添加新成绩统计项
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    系统设置/成绩统计项设置
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>名称</th>
                    <th>所属学校</th>
                    <th>统计项起始分数</th>
                    <th>统计项截止分数</th>
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