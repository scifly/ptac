@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('students/create') }}" class="btn btn-primary pull-right">
                添加新学员
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    系统设置/学生设置
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>学生姓名</th>
                    <th>班级名称</th>
                    <th>学号</th>
                    <th>卡号</th>
                    <th>是否住校</th>
                    <th>生日</th>
                    <th>备注</th>
                    <th>创建时间</th>
                    <th>更新时间</th>
                    <th>状态</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    @isset($show)
    @include('student.show')
    @endif
@endsection