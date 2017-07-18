@extends('layouts.master')
@section('header')
    <h1>添加企业</h1>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">

                </div>
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('name', '名称', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-3">
                            {!! Form::text('name', null, [
                            'class' => 'form-control',
                            'placeholder' => '企业名称（例：凌凯，统一...）',
                            'data-parsley-required' => 'true'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('corpid', 'CorpId', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-3">
                            {!! Form::text('corpid', null, [
                            'class' => 'form-control',
                            'data-parsley-required' => 'true'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::radio('enabled', '1', true) !!}
                        {!! Form::label('enabled', '启用') !!}
                        {!! Form::radio('enabled', '0') !!}
                        {!! Form::label('enabled', '禁用') !!}
                    </div>
                    <div class="form-group">
                        <div class="col-md-3 col-md-offset-4">
                            {!! Form::reset('取消',['class' => 'btn btn-default pull-left']) !!}
                            {!! Form::submit('保存',['class' => 'btn btn-primary pull-right']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection