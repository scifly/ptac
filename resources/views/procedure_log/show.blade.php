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
                        @if ($val->step_status == 2 && in_array($user_id,explode(',', $val->procedure_step->approver_user_ids)))
                        {!! Form::open(['method' => 'post','id' => 'formProcedureLogDecision','data-parsley-validate' => 'true']) !!}
                            <input type="hidden" name="id" value="{{ $val->id }}">
                            <input type="hidden" name="procedure_id" value="{{ $val->procedure_id }}">
                            <input type="hidden" name="procedure_step_id" value="{{ $val->procedure_step_id }}">
                            <input type="hidden" name="first_log_id" value="{{ $val->first_log_id }}">
                            <input type="hidden" name="initiator_user_id" value="{{ $val->initiator_user_id }}">
                            <input type="hidden" name="initiator_msg" value="{{ $val->initiator_msg }}">
                            <input type="hidden" name="initiator_media_ids" value="{{ $val->initiator_media_ids }}">
                            <input type="hidden" name="step_status" value="1" id="step_status">
                            <br>
                            <h3 style="text-align: center;">审核</h3>
                            <div class="row clearfix">
                                <div class="col-sm-4 text-right">留言：</div>
                                <div class="col-sm-3">
                                    {!! Form::text('operator_msg', null, [
                                    'class' => 'form-control text-blue',
                                    'placeholder' => '请输入留言',
                                    'required' => 'true',
                                    ]) !!}
                                </div>
                            </div>
                            <br>
                            <div class="row clearfix">
                                <div class="col-sm-4 text-right">附件：</div>
                                <div class="col-sm-8">
                                    <div class="preview"></div>
                                    <a class="btn btn-primary" data-toggle="modal" data-target="#modalPic">上传</a>
                                </div>
                            </div>
                            <br>
                            <div class="row clearfix">
                                <div class="col-sm-3 col-sm-offset-4" id="submitButton">
                                    <input type="submit" value="通过" class="btn btn-primary pull-left" id="save">
                                    <input type="submit" value="拒绝" class="btn btn-default pull-right" id="cancel">
                                </div>
                            </div>
                        {!! Form::close() !!}
                            <div class="modal fade" id="modalPic">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                    aria-hidden="true">×
                                            </button>
                                            <h4 class="modal-title" id="myModalLabel">
                                                上传附件
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <input type="file" name="medias[]" id="uploadFile" multiple>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection