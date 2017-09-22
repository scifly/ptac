{{--@extends('layouts.master')--}}
{{--@section('header')--}}
{{--<a href="{{ url('/') }}">Back to overview</a>--}}
{{--<h2>--}}
{{--角色详情--}}
{{--</h2>--}}
{{--<a href="{{ url('groups/edit/' . $group->id . '') }}">--}}
{{--<span class="glyphicon glyphicon-edit"></span>--}}
{{--Edit--}}
{{--</a>--}}
{{--<a href="{{ url('groups/delete/'.$group->id ) }}">--}}
{{--<span class="glyphicon glyphicon-trash"></span>--}}
{{--Delete--}}
{{--</a>--}}
{{--<p>Last edited: {{ $group->updated_at->diffForHumans() }}</p>--}}
{{--@endsection--}}
{{--@section('content')--}}
{{--<p>角色名称：{{ $group->name }}</p>--}}
{{--<p>备注：{{ $group->remark }}</p>--}}
{{--@endsection--}}
<div class="modal fade" id="modal-show-group">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    权限详情
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="dl-horizontal">
                            <dt>角色名称：</dt>
                            <dt>备注：</dt>
                            <dt>创建时间：</dt>
                            <dt>更新时间：</dt>
                            <dt>状态：</dt>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
