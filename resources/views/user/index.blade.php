@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('users/create') }}" class="btn btn-primary pull-right">
                添加新用户
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    用户管理/用户设置
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>部门</th>
                    <th>用户名</th>
                    <th>头像</th>
                    <th>姓名</th>
                    <th>性别</th>
                    <th>用户邮箱</th>
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