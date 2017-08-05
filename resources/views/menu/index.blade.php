@extends('layouts.master')
@section('header')
    <div class="panel-heading">
        <div class="btn-group">
            <a href="{{ url('menus/create') }}" class="btn btn-primary pull-right">
                添加新新菜单
            </a>
        </div>
    </div>
@endsection
@section('breadcrumb')
    菜单设置/菜单管理
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info" data-sortable-id="tree-view-1">
            <div class="panel-heading">
                <h4 class="panel-title">菜单管理</h4>
            </div>
            <div class="panel-body">
                <div id="jstree-menu" class="col-md-12"></div>
                <div class="col-md-6" id="menuFormData" style="display:none;">
                    <div class="box box-primary">
                        <div class="box-body">
                            @include('menu.create_edit')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection