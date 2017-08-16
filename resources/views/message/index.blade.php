@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('messages/create') }}" class="btn btn-primary pull-right">
                添加消息
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    消息中心/消息管理
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>消息内容</th>
                    <th>url</th>
                    <th>发送者用户</th>
                    <th>消息类型</th>
                    <th>已读数量</th>
                    <th>消息发送成功数</th>
                    <th>接收者数量</th>
                    <th>创建时间</th>
                    <th>更新时间</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection