@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group pull-right">
            <a href="{{ url('schools/create') }}" class="btn btn-primary pull-right">
                添加新学校
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    系统设置/学校设置
@endsection
@section('content)
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>名称</th>
                    <th>类型</th>
                    <th>地址</th>
                    <th>所属企业</th>
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