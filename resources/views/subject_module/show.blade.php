{{--@extends('layouts.master')--}}
{{--@section('header')--}}
    {{--<a href="{{ url('/') }}">Back to overview</a>--}}
    {{--<h2>--}}
       {{--科目次分类详情--}}
    {{--</h2>--}}
    {{--<a href="{{ url('subject_modules/edit/' . $subjectModule->id . '') }}">--}}
        {{--<span class="glyphicon glyphicon-edit"></span>--}}
        {{--Edit--}}
    {{--</a>--}}
    {{--<a href="{{ url('subject_modules/delete/' . $subjectModule->id . '') }}">--}}
        {{--<span class="glyphicon glyphicon-trash"></span>--}}
        {{--Delete--}}
    {{--</a>--}}
    {{--<p>Last edited: {{ $subjectModule->updated_at->diffForHumans() }}</p>--}}
{{--@endsection--}}
{{--@section('content')--}}
    {{--<p>科目名称：{{ $subjectModule->subject->name }}</p>--}}
    {{--<p>名称：{{ $subjectModule->name }}</p>--}}
    {{--<p>次分类权重：{{ $subjectModule->weight }}</p>--}}
{{--@endsection--}}
<div class="modal fade" id="modal-show-subject_module">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    科目次分类详情
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="dl-horizontal">
                            <dt>科目名称：</dt>
                            <dt>名称：</dt>
                            <dt>次分类权重：</dt>
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
