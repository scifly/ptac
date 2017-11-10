{{--@extends('layouts.master')--}}
{{--@section('header')--}}
{{--<a href="{{ url('/') }}">Back to overview</a>--}}
{{--<h2>--}}
{{--{{ $am->name }}--}}
{{--</h2>--}}
{{--<a href="{{ url('attendance_machines/edit/'. $am->id) }}">--}}
{{--<span class="glyphicon glyphicon-edit"></span>--}}
{{--Edit--}}
{{--</a>--}}
{{--<a href="{{ url('attendance_machines/index') }}">--}}
{{--<span class="glyphicon glyphicon-edit"></span>--}}
{{--index--}}
{{--</a>--}}
{{--<a href="{{ url('attendance_machines/delete/'. $am->id) }}">--}}
{{--<span class="glyphicon glyphicon-trash"></span>--}}
{{--Delete--}}
{{--</a>--}}
{{--<p>Last edited: {{ $am->updated_at->diffForHumans() }}</p>--}}
{{--@endsection--}}
{{--@section('content')--}}
{{--<p>所属位置：{{ $am->location }}</p>--}}
{{--<p>--}}
{{--@if ($am->school_id)--}}
{{--所属学校:--}}
{{--{{ link_to('schools/show/' . $am->school->id, $am->school->name) }}--}}
{{--@endif--}}
{{--</p>--}}
{{--@endsection--}}

<div class="modal fade" id="modal-show-attendancemachine">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    考勤机详情
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="dl-horizontal">
                            <dt>考勤机名称：</dt>
                            <dt>位置：</dt>
                            <dt>所属学校：</dt>
                            <dt>设备编号：</dt>
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