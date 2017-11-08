{{--@extends('layouts.master')--}}
{{--@section('header')--}}
{{--<a href="{{ url('/') }}">Back to overview</a>--}}
{{--<h2>--}}
{{--{{ $procedure->name }}--}}
{{--</h2>--}}
{{--<a href="{{ url('procedures/edit/'. $procedure->id) }}">--}}
{{--<span class="glyphicon glyphicon-edit"></span>--}}
{{--Edit--}}
{{--</a>--}}
{{--<a href="{{ url('procedures/index') }}">--}}
{{--<span class="glyphicon glyphicon-trash"></span>--}}
{{--index--}}
{{--</a>--}}
{{--<a href="{{ url('procedures/delete/'. $procedure->id) }}">--}}
{{--<span class="glyphicon glyphicon-trash"></span>--}}
{{--Delete--}}
{{--</a>--}}
{{--<p>Last edited: {{ $procedure->updated_at->diffForHumans() }}</p>--}}
{{--@endsection--}}
{{--@section('content')--}}
{{--<p>备注：{{ $procedure->remark }}</p>--}}
{{--<p>--}}
{{--@if ($procedure->procedure_type_id)--}}
{{--流程类型:--}}
{{--{{ link_to('procedure_types/show/' . $procedure->procedureType->id, $procedure->procedureType->name) }}--}}
{{--@endif--}}
{{--</p>--}}
{{--<p>--}}
{{--@if ($procedure->school_id)--}}
{{--所属学校:--}}
{{--{{ link_to('schools/show/' . $procedure->school->id, $procedure->school->name) }}--}}
{{--@endif--}}
{{--</p>--}}
{{--@endsection--}}

<div class="modal fade" id="modal-show-procedure">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    流程详情
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="dl-horizontal">
                            <dt>流程名称：</dt>
                            <dt>所属学校：</dt>
                            <dt>所属类型：</dt>
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