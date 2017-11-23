{{--@extends('layouts.master')--}}
{{--@section('header')--}}
{{--<a href="{{ url('/') }}">Back to overview</a>--}}
{{--<h2>--}}
{{--{{ $pt->name }}--}}
{{--</h2>--}}
{{--<a href="{{ url('procedure_types/edit/'. $pt->id ) }}">--}}
{{--<span class="glyphicon glyphicon-edit"></span>--}}
{{--Edit--}}
{{--</a>--}}
{{--<a href="{{ url('procedure_types/index') }}">--}}
{{--<span class="glyphicon glyphicon-trash"></span>--}}
{{--index--}}
{{--</a>--}}
{{--<a href="{{ url('procedure_types/delete/'. $pt->id ) }}">--}}
{{--<span class="glyphicon glyphicon-trash"></span>--}}
{{--Delete--}}
{{--</a>--}}
{{--<p>Last edited: {{ $pt->updated_at->diffForHumans() }}</p>--}}
{{--@endsection--}}
<div class="modal fade" id="modal-show-proceduretype">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    流程类型详情
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="dl-horizontal">
                            <dt>类型名称：</dt>
                            <dt>备注：</dt>
                            <dt>创建于：</dt>
                            <dt>更新于：</dt>
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