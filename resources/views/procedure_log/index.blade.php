@extends('layouts.master')
@section('breadcrumb')
    流程管理/流程日志查看
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>发起人</th>
                    <th>流程</th>
                    <th>步骤</th>
                    <th>发起人留言</th>
                    <th>最新更新时间</th>
                    <th>操作状态</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection