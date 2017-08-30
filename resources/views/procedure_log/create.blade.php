<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
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
                    <div class="preview"></div>
                    <a class="btn btn-primary" data-toggle="modal" data-target="#modalPic">上传</a>
                </div>
            </div>
        </div>
    </div>
    @include('partials.form_buttons')
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
