@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('procedure_types/create') }}" class="btn btn-primary pull-right">
                添加新流程类型
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    流程管理/流程设置
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>名称</th>
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
        @include('procedure_type.show')
    @endif
@endsection