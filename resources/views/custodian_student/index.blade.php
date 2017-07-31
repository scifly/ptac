@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('custodianStudents/create') }}" class="btn btn-primary pull-right">
                添加监护人
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    用户/通讯录/监护人设置
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>监护人姓名</th>
                    <th>学生姓名</th>
                    <th>关系</th>
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