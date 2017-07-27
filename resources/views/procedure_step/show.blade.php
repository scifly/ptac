@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $procedureStep->name }}
    </h2>
    <a href="{{ url('procedure_steps/edit/'. $procedureStep->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('procedure_steps/index') }}">
        <span class="glyphicon glyphicon-trash"></span>
        index
    </a>
    {{--<a href="{{ url('procedure_types/delete/'. $pt->id ) }}">--}}
        {{--<span class="glyphicon glyphicon-trash"></span>--}}
        {{--Delete--}}
    {{--</a>--}}
    <p>Last edited: {{ $procedureStep->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>
        @if ($procedureStep->procedure_id)
            流程:
            {{ link_to('procedures/show/' . $procedureStep->procedure->id, $procedureStep->procedure->name) }}
        @endif
    </p>
    <p>
        @if ($procedureStep->approver_user_ids)
            审批用户:

             @foreach ($procedureStep->approvers_show($procedureStep->approver_user_ids) as $user)

                {{ link_to('users/show/' .  $user->id,$user->realname)}}|

            @endforeach

        @endif
    </p>
    <p>
        @if ($procedureStep->related_user_ids)
            相关人员:

            @foreach ($procedureStep->related_users_show($procedureStep->related_user_ids) as $user)

                {{ link_to('users/show/' .  $user->id,$user->realname)}}|

            @endforeach

        @endif
    </p>
    <p>备注：{{ $procedureStep->remark }}</p>
@endsection