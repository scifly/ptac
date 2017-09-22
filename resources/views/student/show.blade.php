{{--@extends('layouts.master')--}}
{{--@section('header')--}}
{{--<a href="{{ url('/') }}">Back to overview</a>--}}
{{--<h2>--}}
{{--学生详情--}}
{{--</h2>--}}
{{--<a href="{{ url('students/edit/' . $student->id . '') }}">--}}
{{--<span class="glyphicon glyphicon-edit"></span>--}}
{{--Edit--}}
{{--</a>--}}
{{--<a href="{{ url('students/delete/' . $student->id . '') }}">--}}
{{--<span class="glyphicon glyphicon-trash"></span>--}}
{{--Delete--}}
{{--</a>--}}
{{--<p>Last edited: {{ $student->updated_at->diffForHumans() }}</p>--}}
{{--@endsection--}}
{{--@section('content')--}}
{{--<p>学生姓名：{{ $student->user->realname }}</p>--}}
{{--<p>班级名称：{{ $student->squad->name }}</p>--}}
{{--<p>学号：{{ $student->student_number }}</p>--}}
{{--<p>卡号：{{ $student->card_number }}</p>--}}
{{--<p>是否住校：{{ $student->oncampus==1 ? '是' : '否' }}</p>--}}
{{--<p>生日：{{ $student->birthday }}</p>--}}
{{--<p>备注：{{ $student->remark }}</p>--}}
{{--@endsection--}}
<div class="modal fade" id="modal-show-student">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    科目详情
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="dl-horizontal">
                            <dt>学生姓名：</dt>
                            <dt>性别：</dt>
                            <dt>班级名称：</dt>
                            <dt>学号：</dt>
                            <dt>卡号：</dt>
                            <dt>是否住校：</dt>
                            <dt>手机号码：</dt>
                            <dt>生日：</dt>
                            <dt>备注：</dt>
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