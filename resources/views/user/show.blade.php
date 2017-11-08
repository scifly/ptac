{{--@extends('layouts.master')--}}
{{--@section('header')--}}
{{--<a href="{{ url('/') }}">Back to overview</a>--}}
{{--<h2>--}}
{{--用户名：--}}
{{--{{ $user->username }}--}}
{{--</h2>--}}
{{--<a href="{{ url('users/edit/'. $user->id) }}">--}}
{{--<span class="glyphicon glyphicon-edit"></span>--}}
{{--Edit--}}
{{--</a>--}}
{{--<a href="{{ url('users/index') }}">--}}
{{--<span class="glyphicon glyphicon-edit"></span>--}}
{{--index--}}
{{--</a>--}}
{{--<p>--}}
{{--头像：--}}
{{--{{ $user->avatar_url }}--}}
{{--</p>--}}
{{--<p>--}}
{{--姓名：--}}
{{--{{ $user->realname }}--}}
{{--</p>--}}
{{--<p>--}}
{{--性别：--}}
{{--{{ $gender }}--}}
{{--</p>--}}
{{--<p>--}}
{{--微信号：--}}
{{--{{ $user->wechaatid }}--}}
{{--</p>--}}
{{--<p>Last edited: {{ $user->updated_at->diffForHumans() }}</p>--}}
{{--@endsection--}}
{{--@section('content')--}}
{{--<p>--}}
{{--@if ($user->group_id)--}}
{{--所属组别:--}}
{{--{{ link_to('groups/show/' . $user->group->id, $user->group->name) }}--}}
{{--@endif--}}
{{--</p>--}}
{{--@endsection--}}

<div class="modal fade" id="modal-show-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    用户详情
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="dl-horizontal">
                            <dt>用户名：</dt>
                            <dt>角色：</dt>
                            <dt>姓名：</dt>
                            <dt>性别：</dt>
                            <dt>邮箱：</dt>
                            <dt>微信号：</dt>
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