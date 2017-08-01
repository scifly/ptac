@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $procedureLog->name }}
    </h2>
    <a href="{{ url('procedure_logs/index') }}">
        <span class="glyphicon glyphicon-trash"></span>
        index
    </a>
    <p>Last edited: {{ $procedureLog->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>
        @if ($procedureLog->procedure_id)
            流程:
            {{ link_to('procedures/show/' . $procedureLog->procedure->id, $procedureLog->procedure->name) }}
        @endif
    </p>
    <p>
        @if ($procedureLog->procedure_step_id)
            流程步骤:
            {{ link_to('procedure_steps/show/' . $procedureLog->procedureStep->id, $procedureLog->procedureStep->name) }}
        @endif
    </p>
    <p>
        @if ($procedureLog->initiator_user_id)
            发起人员:
            {{ link_to('users/show/' . $initiator_user->id, $initiator_user->realname) }}
        @endif
    </p>
    <p>
        发起人留言：
       {{$procedureLog->initiator_msg}}
    </p>
    <p>
        发起人媒体信息：$initiator_medias 信息->尚未进行处理。
        {{--{{$procedureLog->initiator_msg}}--}}
    </p>
    <p>
        @if ($procedureLog->operator_user_id)
            操作人员:
            {{ link_to('users/show/' . $operator_user->id, $operator_user->realname) }}
        @endif
    </p>
    <p>
        操作人留言：
        {{$procedureLog->operator_msg}}
    </p>
    <p>
        操作人媒体信息：$operator_medias 信息->尚未进行处理。
        {{--        {{$procedureLog->operator_msg}}--}}
    </p>
    <p>
       审核状态：
        {{$procedureLog->status($procedureLog->step_status)}}
    </p>
@endsection