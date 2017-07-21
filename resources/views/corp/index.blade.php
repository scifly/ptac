@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('corps/create') }}" class="btn btn-primary pull-right">
                添加新企业
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    系统设置/企业设置
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>名称</th>
                    <th>所属运营者</th>
                    <th>企业号ID</th>
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