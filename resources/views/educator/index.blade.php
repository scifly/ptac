@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('educators/create') }}" class="btn btn-primary pull-right">
                添加新教职员工
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    用户/通信录管理/教职员工
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>教职工名称</th>
                    <th>所属组</th>
                    <th>所属学校</th>
                    <th>可用短信条数</th>
                    <th>创建时间</th>
                    <th>更新时间</th>
                    <th></th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection