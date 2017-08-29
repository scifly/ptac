<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.list_header')
    </div>
    <div class="box-body">
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


{{--@extends('layouts.master')--}}
{{--@section('header')--}}
    {{--<div class="panel-heading">--}}
        {{--<div class="btn-group">--}}
            {{--<a href="{{ url('icon_types/create') }}" class="btn btn-primary pull-right">--}}
                {{--添加新Icon类型--}}
            {{--</a>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--@endsection--}}
{{--@section('breadcrumb')--}}
    {{--菜单管理/图标类型设置--}}
{{--@endsection--}}
{{--@section('content')--}}
    {{--<div class="panel-body">--}}
        {{--<div class="table-responsive">--}}
            {{--<table id="data-table" class="table table-striped table-bordered table-hover table-condensed">--}}
                {{--<thead>--}}
                {{--<tr>--}}
                    {{--<th>#</th>--}}
                    {{--<th>名称</th>--}}
                    {{--<th>备注</th>--}}
                    {{--<th>创建时间</th>--}}
                    {{--<th>更新时间</th>--}}
                    {{--<th>状态</th>--}}
                {{--</tr>--}}
                {{--</thead>--}}
                {{--<tbody></tbody>--}}
            {{--</table>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--@endsection--}}