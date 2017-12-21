<div class="form-horizontal" id="imagetext" style="display: none;">
    {!! Form::open([
        'method' => 'post',
        'id' => 'formImagetext',
        'data-parsley-validate' => 'true'
    ]) !!}
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
                'class' => 'form-control',
            ]) !!}
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3"></label>
        <div class="col-sm-6">
            <a href="#"><i class="fa fa-paperclip text-blue"></i>&nbsp;添加附件</a>
            <a href="#"><i class="fa fa-link text-blue"></i>&nbsp;添加原文链接</a>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3"></label>
        <div class="col-sm-6">
            <p>
                <a href="#">添加封面图</a>&nbsp;&nbsp;<span class="text-gray">建议尺寸:1068*534</span>
            </p>
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('title', '摘要', [
            'class' => 'col-sm-3 control-label'
        ]) !!}
        <div class="col-sm-6">
            {!! Form::text('content_image', null, [
                'class' => 'form-control',
                'placeholder' => '(如不填会自动抓取正文前54字)',
            ]) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('title', '作者', [
            'class' => 'col-sm-3 control-label'
        ]) !!}
        <div class="col-sm-6">
            {!! Form::text('content_image', null, [
                'class' => 'form-control',
                'placeholder' => '(选填)',
            ]) !!}
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3"></label>
        <div class="col-sm-6">
            <input class="btn btn-default pull-right margin" id="cancel-imagetext" type="reset" value="取消">
            <input type="button" class="btn btn-primary pull-right margin" id="save-imagetext" value="确认">
        </div>
    </div>
    {!! Form::close() !!}
</div>