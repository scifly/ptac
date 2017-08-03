@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('wsmarticles/create') }}" class="btn btn-primary pull-right">
                添加文章
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    自媒体管理/微网站管理
@endsection
@section('content')
    <div class="panel-body">
        <div class="table-responsive">
            <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
                <thead>
                <tr>
                    <th>#</th>
                    <th>所属网站模块</th>
                    <th>文章名称</th>
                    <th>文章摘要</th>
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