<div class="form-horizontal" id="upload_video" style="display: none;">
    {!! Form::open([
        'method' => 'post',
        'id' => 'formVideo',
        'data-parsley-validate' => 'true'
    ]) !!}
    <div class="form-group">
        {!! Form::label('title', '标题', [
            'class' => 'col-sm-3 control-label'
        ]) !!}
        <div class="col-sm-6">
            {!! Form::text('content_video', null, [
                'class' => 'form-control video-title',
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
            <p id="filevideo">
            	<form id="form-cover" enctype="multipart/form-data">
                <span class="text-gray" style="display: block">tips：视频格式支持mp4，大小不能超过10MB</span>
                <a href="#" style="position: relative;">
                		添加视频
                		<input type="hidden" value="video" name="type" />
                        <input type="file" id="file-video" onchange="uploadFile(this)" name="input-video" accept="video/mp4" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>
                	</a>
               </form>
            </p>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('description', '描述', [
            'class' => 'col-sm-3 control-label'
        ]) !!}
        <div class="col-sm-6">
            {!! Form::text('content_video', null, [
                'class' => 'form-control imagetext-description',
                'placeholder' => '(选填)',
            ]) !!}
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3"></label>
        <div class="col-sm-6">
            <input class="btn btn-default pull-right margin" id="cancel-video" type="reset" value="取消">
            <input type="button" class="btn btn-primary pull-right margin" id="save-video" value="确认">
        </div>
    </div>
    {!! Form::close() !!}
</div>