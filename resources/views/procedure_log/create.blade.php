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
                    {!! Form::label('procedure_id', '请选择申请项目',['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::select('procedure_id', $procedure_id, null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('initiator_msg', '留言',['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::text('initiator_msg', null, [
                        'class' => 'form-control',
                        'placeholder' => '请输入留言',
                        'data-parsley-required' => 'true',
                        ]) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label for="media_ids" class="col-sm-2 control-label">附件</label>
                    <div class="col-sm-8">
                        <div class="preview pull-left">
                            <div class="img-item">
                                <img src="../img/excel_128px.png" alt="">
                                <div class="del-mask">
                                    <span class="file-name">名字名字名字</span>
                                    <i class="delete fa fa-trash-o"></i>
                                </div>
                            </div>
                            <div class="img-item">
                                <img src="../img/pdf_128px.png" alt="">
                                <div class="del-mask">
                                    <i class="delete fa fa-trash-o"></i>
                                </div>
                            </div>
                            <div class="img-item">
                                <img src="../img/txt_128px.png" alt="">
                                <div class="del-mask">
                                    <i class="delete fa fa-trash-o"></i>
                                </div>
                            </div>
                            <div class="img-item">
                                <img src="../img/word_128px.png" alt="">
                                <div class="del-mask">
                                    <i class="delete fa fa-trash-o"></i>
                                </div>
                            </div>
                            <div class="img-item">
                                <img src="../img/zip_128px.png" alt="">
                                <div class="del-mask">
                                    <span></span>
                                    <i class="delete fa fa-trash-o"></i>
                                </div>
                            </div>
                        </div>
                        <a class="btn btn-primary" data-toggle="modal" data-target="#modalPic">上传</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            {{--button--}}
            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-2">
                    {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'save']) !!}
                    {!! Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}
                </div>
            </div>
        </div>
    </div>

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

    {!! Form::close() !!}
@endsection