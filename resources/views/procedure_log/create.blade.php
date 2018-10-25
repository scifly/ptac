{!! Form::open([
    'method' => 'post',
    'id' => 'formProcedureLogCreate',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @include('shared.single_select', [
                'label' => '选择申请项目',
                'id' => 'procedure_id',
                'items' => $procedure_id
            ])
            <div class="form-group">
                {!! Form::label('initiator_msg', '留言',['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::text('initiator_msg', null, [
                    'class' => 'form-control text-blue',
                    'placeholder' => '请输入留言',
                    'required' => 'true'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="media_ids" class="col-sm-3 control-label">附件</label>
                <div class="col-sm-6">
                    <div class="preview"></div>
                    <a class="btn btn-primary" data-toggle="modal" data-target="#modalPic">上传</a>
                </div>
            </div>
        </div>
    </div>
    @include('shared.form_buttons')
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
