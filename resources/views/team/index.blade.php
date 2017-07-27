@extends('layouts.master')
@section('header')
    教职员工组设置
@endsection
@section('breadcrumb')
    系统设置/教职员工组设置
@endsection
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <div class="btn-group">
                        <a href="{{ url('teams/create') }}" class="btn btn-primary pull-right">
                            <i class="fa fa-plus"></i>
                            添加教职员工组
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <table id="data-table" class="dataTable table table-striped table-hover table-bordered">
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
        </div>
    </div>
@endsection