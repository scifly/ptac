@extends('layouts.master')
@section('header')
    <h1>审批详情</h1>
@endsection
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="stepFlex clearfix">
                @foreach ($data as $key => $val)
                    <dl class="flex-item @if ($val->step_status == 0)success @elseif ($val->step_status == 1)fail @else doing @endif">
                        <dt class="s-num">{{ $key+1 }}</dt>
                        <dd class="s-text">{{ $val->procedure_step->name }}</dd>
                    </dl>
                @endforeach
            </div>
            <div class="stepInfo">
                @foreach ($data as $key => $val)
                    <div class="info-item">
                        <h3 style="text-align: center;">{{ $key+1 }}</h3>
                        <div class="row clearfix">
                            <div class="col-sm-4 text-right">流程：</div>
                            <div class="col-sm-8">{{ link_to('procedures/show/' . $val->procedure_id, $val->procedure->name) }}</div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-4 text-right">流程步骤：</div>
                            <div class="col-sm-8">{{ link_to('procedure_steps/show/' . $val->procedure_step_id, $val->procedure_step->name) }}</div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-4 text-right">发起人员：</div>
                            <div class="col-sm-8">{{ link_to('users/show/' . $val->initiator_user_id, $val->initiator_user->realname) }}</div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-4 text-right">发起人留言：</div>
                            <div class="col-sm-8">{{ $val->initiator_msg }}</div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-4 text-right">发起人媒体信息：</div>
                            <div class="col-sm-8">
                                $initiator_medias 信息->尚未进行处理。
                                {{--{{$procedureLog->initiator_msg}}--}}
                            </div>
                        </div>
                        @if ($val->operator_user_id)
                        <div class="row clearfix">
                            <div class="col-sm-4 text-right">操作人员：</div>
                            <div class="col-sm-8">{{ link_to('users/show/' . $val->operator_user_id, $val->operator_user->realname) }}</div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-4 text-right">操作人留言：</div>
                            <div class="col-sm-8">{{ $val->operator_msg }}</div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-4 text-right">操作人媒体信息：</div>
                            <div class="col-sm-8">
                                $operator_medias 信息->尚未进行处理。
                                {{--{{$procedureLog->operator_msg}}--}}
                            </div>
                        </div>
                        @endif
                        <div class="row clearfix">
                            <div class="col-sm-4 text-right">审核状态：</div>
                            <div class="col-sm-8">@if ($val->step_status == 0)通过 @elseif ($val->step_status == 1)拒绝 @else 尚未处理 @endif</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection