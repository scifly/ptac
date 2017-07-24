@extends('layouts.master')
@section('header')
<h1> 角色权限管理 </h1>
@endsection
@section('content')
    <div class="panel-heading">
        <a href="javascript:" class="btn btn-primary">
            <i class="glyphicon glyphicon-plus"></i> 添加
        </a>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-hover table-bordered table-striped table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>角色名称</th>
                    <th>备注</th>
                    <th>创建时间</th>
                    <th>修改时间</th>
                    <th>启用</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection
