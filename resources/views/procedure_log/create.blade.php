@extends('layouts.master')
@section('header')
    <h1>发起审批</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formProcedureLogCreate','data-parsley-validate' => 'true']) !!}
    <div class="box box-primary">
        <div class="box-header"></div>
        <div class="box-body">
            <div class="form-horizontal">

                <div class="form-group">
                    {!! Form::label('procedure_id', '请选择申请项目',['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::select('procedure_id', $procedure_id, null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('initiator_msg', '留言',['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::text('initiator_msg', null, [
                        'class' => 'form-control',
                        'placeholder' => '请输入留言',
                        'data-parsley-required' => 'true',
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            {{--button--}}
            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-4">
                    {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'save']) !!}
                    {!! Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@endsection