<div class="modal fade" id="modal-textcard">
    {!! Form::open([
        'method' => 'post',
        'id' => 'formTextcard',
        'data-parsley-validate' => 'true'
    ]) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">添加卡片</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal" id="create_textcard">
                    <!-- 标题 -->
                    <div class="form-group">
                        {!! Form::label('textcard-title', '标题', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::text('textcard-title', null, [
                                'class' => 'form-control',
                                'placeholder' => '(请输入标题)',
                                'required' => 'true',
                                'data-parsley-length' => '[2,10]',
                                'maxlength' => '128',
                            ]) !!}
                        </div>
                    </div>
                    <!-- 描述 -->
                    <div class="form-group">
                        {!! Form::label('textcard-description', '描述', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::textarea('description', null, [
                                'required' => 'true',
                                'maxlength' => 255,
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                    </div>
                    <!-- URL -->
                    <div class="form-group">
                        {!! Form::label('textcard-url', '描述', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::textarea('description', null, [
                                'required' => 'true',
                                'maxlength' => 255,
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                    </div>
                    <!-- 按钮文字 -->
                    <div class="form-group">
                        {!! Form::label('textcard-url', '描述', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            {!! Form::textarea('description', null, [
                                'required' => 'true',
                                'maxlength' => 255,
                                'class' => 'form-control',
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