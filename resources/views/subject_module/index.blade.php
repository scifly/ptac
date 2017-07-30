@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('subjectModules/create') }}" class="btn btn-primary pull-right">
                添加次分类
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    系统设置/科目次分类
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>科目名称</th>
                    <th>次分类名称</th>
                    <th>次分类权重</th>
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