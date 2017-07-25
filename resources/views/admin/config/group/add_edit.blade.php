@extends('layouts.master')
@section('header')
    <h1>添加角色权限</h1>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading"></div>
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('name', '名称', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-3">
                            {!! Form::text('name', null, [
                            'class' => 'form-control',
                            'placeholder' => '角色名称（例：经理、库管、前台...）',
                            'data-parsley-required' => 'true'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('remark', '备注', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-3">
                            {!! Form::text('remark', null, [
                            'class' => 'form-control',
                            'type' => 'textarea',
                            'placeholder' => '备注',
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
                            {!! Form::reset('取消', ['class'=>'btn btn-primary pull-left']) !!}
                            {!! Form::submit('保存', ['class'=>'btn btn-default pull-right']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection