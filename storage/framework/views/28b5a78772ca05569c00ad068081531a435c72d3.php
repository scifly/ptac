<div class="form-horizontal" id="imagetext" style="display: none;">
    <?php echo Form::open([
        'method' => 'post',
        'id' => 'formImagetext',
        'data-parsley-validate' => 'true'
    ]); ?>

    <div class="form-group">
        <?php echo Form::label('title', '标题', [
            'class' => 'col-sm-3 control-label'
        ]); ?>

        <div class="col-sm-6">
            <?php echo Form::text('content_image', null, [
                'class' => 'form-control imagetext-title',
                'placeholder' => '(请输入标题)',
                'required' => 'true',
                'data-parsley-length' => '[2,10]',
                'maxlength' => '128',
            ]); ?>

        </div>
    </div>
    <div class="form-group">
        <?php echo Form::label('content', '正文', [
            'class' => 'col-sm-3 control-label'
        ]); ?>

        <div class="col-sm-6">
            <?php echo Form::textarea('content', null, [
                'id' => 'content',
                'class' => 'form-control imagetext-content',
                'maxlength' => '666',
            ]); ?>

        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3"></label>
        <div class="col-sm-6">
            <!--<a href="#"><i class="fa fa-paperclip text-blue"></i>&nbsp;添加附件</a>-->
            <a href="#" id="add-article-url"><i class="fa fa-link text-blue"></i>&nbsp;添加原文链接</a>
            <?php echo Form::text('content_image', null, [
                'class' => 'form-control imagetext-content_source_url',
                'placeholder' => '(原文链接)',
                'style' => 'display:none',
            ]); ?>

        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3"></label>
        <div class="col-sm-6">
            <p id="cover">
            	<form id="form-cover" enctype="multipart/form-data">
                	<a href="#" style="position: relative;">
                		添加封面图
                		<input type="hidden" value="image" name="type" />
                        <input type="file" id="file-cover" onchange="upload_cover(this)" name="input-cover" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>
                	</a>
                	&nbsp;&nbsp;<span class="text-gray">建议尺寸:1068*534</span>
                	
               </form>
            </p>
        </div>
    </div>

    <div class="form-group">
        <?php echo Form::label('title', '作者', [
            'class' => 'col-sm-3 control-label'
        ]); ?>

        <div class="col-sm-6">
            <?php echo Form::text('content_image', null, [
                'class' => 'form-control imagetext-author',
                'placeholder' => '(选填)',
            ]); ?>

        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3"></label>
        <div class="col-sm-6">
            <input class="btn btn-default pull-right margin" id="cancel-imagetext" type="reset" value="取消">
            <input type="button" class="btn btn-primary pull-right margin" id="save-imagetext" value="确认">
        </div>
    </div>
    <?php echo Form::close(); ?>

</div>