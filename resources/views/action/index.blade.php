@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('actions/create') }}" class="btn btn-primary pull-right">
                添加新Action
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    菜单管理/Action设置
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>名称</th>
                    <th>方法</th>
                    <th>控制器</th>
                    <th>view路径</th>
                    <th>js路径</th>
                    <th>创建时间</th>
                    <th>更新时间</th>
                    <th>请求类型</th>
                    <th>状态</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection