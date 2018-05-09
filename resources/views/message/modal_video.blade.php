<div class="modal fade" id="modal-video">
    {!! Form::open([
        'method' => 'post',
        'id' => 'formVideo',
        'data-parsley-validate' => 'true'
    ]) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">添加视频</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal" id="upload_video">
                    <div class="form-group">
                        {!! Form::label('title', '标题', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('content_video', null, [
                                'class' => 'form-control',
                                'placeholder' => '(请输入标题)',
                                'required' => 'true',
                                'data-parsley-length' => '[2,10]',
                                'maxlength' => '128',
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3"></label>
                        <div class="col-sm-6">
                            <span class="text-gray" style="display: block">
                                tips：视频格式支持mp4，大小不能超过10MB
                            </span>
                            <a href="#" style="position: relative;">
                                上传视频
                                {!! Form::hidden('type') !!}
                                {!! Form::file('file-video', [
                                    'id' => 'file-video',
                                    'accept' => 'video/*',
                                    'class' => 'upload'
                                ]) !!}
                            </a>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('description', '描述', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('content_video', null, [
                                'class' => 'form-control',
                                'placeholder' => '(选填)',
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
                <a id="save-video" href="#" class="btn btn-sm btn-success" data-dismiss="modal">确定</a>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>