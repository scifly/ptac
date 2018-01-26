<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(isset($article) && !empty($article['id'])): ?>
                <?php echo e(Form::hidden('id', $article['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <?php echo $__env->make('partials.single_select', [
                'label' => '所属网站模块',
                'id' => 'wsm_id',
                'items' => $wsms
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="form-group">
                <?php echo Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-3">
                    <?php echo Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过40个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 40]'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('summary', '文章摘要', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-3">
                    <?php echo Form::text('summary', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过60个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('media_ids', '轮播图', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="preview">
                        <?php if(isset($medias)): ?>
                            <?php $__currentLoopData = $medias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!empty($value)): ?>
                                    <div class="img-item">
                                        <img src="../../<?php echo e($value->path); ?>" id="<?php echo e($value->id); ?>">
                                        <input type="hidden" name="media_ids[]" value="<?php echo e($value->id); ?>"/>
                                        <div class="del-mask">
                                            <i class="delete fa fa-trash"></i>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                    <a class="btn btn-primary" data-toggle="modal" data-target="#modalPic">上传</a>
                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('content', '文章内容', ['class' => 'control-label col-sm-3']); ?>

                <div class="col-sm-6">
                    <div class="preview_content">
                        <script id="container" name="content" type="text/plain" >
                            <?php if(isset($article)): ?>
                                <?php echo $article['content']; ?>

                            <?php endif; ?>
                        </script>
                    </div>
                </div>
            </div>
                <?php echo $__env->make('partials.enabled', [
                    'id' => 'enabled',
                    'value' => $article['enabled'] ?? null
                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<div class="modal fade" id="modalPic">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">×
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    模态框（Modal）标题
                </h4>
            </div>
            <div class="modal-body">
                <input type="file" name="img[]" id="uploadFile" accept="image/jpeg,image/gif,image/png" multiple>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">关闭
                </button>
            </div>
        </div>
    </div>
</div>