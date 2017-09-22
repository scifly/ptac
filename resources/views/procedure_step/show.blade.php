{{--@extends('layouts.master')--}}
{{--@section('header')--}}
{{--<a href="{{ url('/') }}">Back to overview</a>--}}
{{--<h2>--}}
{{--{{ $procedureStep->name }}--}}
{{--</h2>--}}
{{--<a href="{{ url('procedure_steps/edit/'. $procedureStep->id ) }}">--}}
{{--<span class="glyphicon glyphicon-edit"></span>--}}
{{--Edit--}}
{{--</a>--}}
{{--<a href="{{ url('procedure_steps/index') }}">--}}
{{--<span class="glyphicon glyphicon-trash"></span>--}}
{{--index--}}
{{--</a>--}}
{{--<a href="{{ url('procedure_types/delete/'. $pt->id ) }}">--}}
{{--<span class="glyphicon glyphicon-trash"></span>--}}
{{--Delete--}}
{{--</a>--}}
{{--<p>Last edited: {{ $procedureStep->updated_at->diffForHumans() }}</p>--}}
{{--@endsection--}}
{{--@section('content')--}}
{{--<p>--}}
{{--@if ($procedureStep->procedure_id)--}}
{{--流程:--}}
{{--{{ link_to('procedures/show/' . $procedureStep->procedure->id, $procedureStep->procedure->name) }}--}}
{{--@endif--}}
{{--</p>--}}
{{--<p>--}}
{{--@if ($procedureStep->approver_user_ids)--}}
{{--审批用户:--}}

{{--@foreach ($approver_user_ids as $user_id => $realname)--}}

{{--{{ link_to('users/show/' .  $user_id,$realname)}}|--}}

{{--@endforeach--}}

{{--@endif--}}
{{--</p>--}}
{{--<p>--}}
{{--@if ($procedureStep->related_user_ids)--}}
{{--相关人员:--}}

{{--@foreach ($related_user_ids as $user_id => $realname)--}}

{{--{{ link_to('users/show/' .  $user_id,$realname)}}|--}}

{{--@endforeach--}}

{{--@endif--}}
{{--</p>--}}
{{--<p>备注：{{ $procedureStep->remark }}</p>--}}
{{--@endsection--}}

<div class="modal fade" id="modal-show-procedurestep">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    流程步骤详情
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="dl-horizontal">
                            <dt>所属流程：</dt>
                            <dt>步骤描述：</dt>
                            <dt>审批用户：</dt>
                            <dt>相关人员：</dt>
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