@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('apps/create') }}" class="btn btn-primary pull-right">
                添加新应用
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    系统设置/应用设置
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>应用名称</th>
                    <th>应用id</th>
                    <th>位置上报</th>
                    <th>用户变更通知</th>
                    <th>事件上报</th>
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