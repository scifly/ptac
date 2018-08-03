<div class="modal fade" id="modal-mpnews">
    {!! Form::open([
        'method' => 'post',
        'id' => 'formMpnews',
        'data-parsley-validate' => 'true'
    ]) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">添加图文</h4>
            </div>
            <div class="modal-body">
                <div class="overlay mpnews-overlay">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
                <div class="form-horizontal">
                    {!! Form::hidden('mpnews-id', null, [
                        'id' => 'mpnews-id'
                    ]) !!}
                    <!-- 标题 -->
                    <div class="form-group">
                        {!! Form::label('mpnews-title', '标题', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('mpnews-title', null, [
                                'id' => 'mpnews-title',
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
                        {!! Form::label('mpnews-content', '正文', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::textarea('mpnews-content', null, [
                                'id' => 'mpnews-content',
                                'class' => 'form-control',
                                'required' => 'true',
                                'rows' => 5,
                                'maxlength' => '666',
                            ]) !!}
                        </div>
                    </div>
                    <!-- 原文链接 -->
                    <div class="form-group">
                        {!! Form::label('content-source-url', '原文链接', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('content-source-url', null, [
                                'id' => 'content-source-url',
                                'class' => 'form-control',
                                'placeholder' => '请在此插入原文链接地址（可选）',
                                'maxlength' => '255',
                                'type' => 'url'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('mpnews-digest', '摘要', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('mpnews-digest', null, [
                                'id' => 'mpnews-digest',
                                'class' => 'form-control',
                                'placeholder' => '(如不填写则自动截取正文前54字)',
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('mpnews-author', '作者', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('mpnews-author', null, [
                                'id' => 'mpnews-author',
                                'class' => 'form-control',
                                'placeholder' => '(可选)',
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('file-mpnews', '封面图', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div id="cover-container" class="col-sm-6">
                            @include('message.file_upload', [
                                'id' => 'file-mpnews',
                                'label' => '上传封面图',
                                'accept' => 'image/*',
                                'note' => '建议尺寸:1068*598',
                                'required' => 'true'
                            ])
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="save-mpnews" type="submit" class="btn btn-success margin btn-sm">
                    <i class="fa fa-save"> 确定</i>
                </button>
                <button id="remove-mpnews" class="btn btn-sm btn-danger margin pull-right"
                        data-dismiss="modal" style="display: none;">
                    <i class="fa fa-times"> 移除当前图文</i>
                </button>
                <button class="btn btn-sm btn-default pull-right margin" data-dismiss="modal">
                    <i class="fa fa-reply"> 取消</i>
                </button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>