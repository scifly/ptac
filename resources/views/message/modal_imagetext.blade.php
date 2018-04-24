<div class="modal fade" id="modal-imagetext">
    {!! Form::open([
        'method' => 'post',
        'id' => 'formImagetext',
        'data-parsley-validate' => 'true'
    ]) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">添加图文</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal" id="imagetext" style="display: none;">
                    <div class="form-group">
                        {!! Form::label('title', '标题', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('content_image', null, [
                                'class' => 'form-control imagetext-title',
                                'placeholder' => '(请输入标题)',
                                'required' => 'true',
                                'data-parsley-length' => '[2,10]',
                                'maxlength' => '128',
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('content', '正文', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::textarea('content', null, [
                                'id' => 'content',
                                'class' => 'form-control imagetext-content',
                                'maxlength' => '666',
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3"></label>
                        <div class="col-sm-6">
                            <!--<a href="#"><i class="fa fa-paperclip text-blue"></i>&nbsp;添加附件</a>-->
                            <a href="#" id="add-article-url"><i class="fa fa-link text-blue"></i>&nbsp;添加原文链接</a>
                            {!! Form::text('content_image', null, [
                                'class' => 'form-control imagetext-content_source_url',
                                'placeholder' => '(原文链接)',
                                'style' => 'display:none',
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3"></label>
                        <div class="col-sm-6">
                            <button id="add-image" class="btn btn-box-tool" type="button"
                                    style="margin-top: 3px; position: relative; border: 0;">
                                <i class="fa fa-plus text-blue">
                                    &nbsp;添加图片
                                    <input type="hidden" value="image" name="type"/>
                                    <input type="file" id="file-image" onchange="uploadFile(this)"
                                           name="uploadFile" accept="image/*"
                                           style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>
                                </i>
                            </button>
                            <input type="file" id="file-cover" onchange="uploadCover(this)" name="input-cover" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('title', '作者', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('content_image', null, [
                                'class' => 'form-control imagetext-author',
                                'placeholder' => '(选填)',
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a id="save-imagetext" href="#" class="btn btn-sm btn-success" data-dismiss="modal">确定</a>
                <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>