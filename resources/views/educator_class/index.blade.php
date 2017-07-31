@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('educatorClasses/create') }}" class="btn btn-primary pull-right">
                添加教职员工
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    系统设置/教职员工
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>教职员工姓名</th>
                    <th>班级名称</th>
                    <th>科目名称</th>
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