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
                <div class="form-horizontal" id="imagetext">
                    <!-- 标题 -->
                    <div class="form-group">
                        {!! Form::label('title', '标题', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('content_image', null, [
                                'class' => 'form-control',
                                'placeholder' => '(请输入标题)',
                                'required' => 'true',
                                'data-parsley-length' => '[2,10]',
                                'maxlength' => '128',
                            ]) !!}
                        </div>
                    </div>
                    <!-- 正文 -->
                    <div class="form-group">
                        {!! Form::label('content', '正文', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::textarea('content', null, [
                                'id' => 'content',
                                'class' => 'form-control',
                                'maxlength' => '666',
                            ]) !!}
                        </div>
                    </div>
                    <!-- 原文链接 -->
                    <div class="form-group">
                        {!! Form::label('content_source_url', '原文链接', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('content_source_url', null, [
                                'id' => 'content_source_url',
                                'class' => 'form-control',
                                'placeholder' => '请在此插入原文链接地址（可选）',
                                'maxlength' => '255',
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3"></label>
                        <div class="col-sm-6">
                            <button id="add-mpnews-image" class="btn btn-box-tool add-btn" type="button">
                                <i class="fa fa-plus text-blue">
                                    &nbsp;添加图片
                                    {!! Form::hidden('type') !!}
                                    {!! Form::file('file-mpnews-image', [
                                        'id' => 'file-mpnews-image',
                                        'accept' => 'image/*',
                                        'class' => 'upload'
                                    ]) !!}
                                </i>
                            </button>
                            {!! Form::file('cover-image', [
                                'id' => 'cover-image',
                                'accept' => 'image/*',
                                'class' => 'upload',
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('title', '作者', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('content_image', null, [
                                'class' => 'form-control imagetext-author',
                                'placeholder' => '(可选)',
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