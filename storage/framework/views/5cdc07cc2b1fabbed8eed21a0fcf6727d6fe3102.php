<div class="modal fade" id="modal-show-event">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    新增列表事件
                </h4>
            </div>
            <div class="modal-body">
                <?php echo Form::open(['method' => 'post', 'id' => 'formEvent', 'data-parsley-validate' => 'true']); ?>

                <div class="form-horizontal">
                    <div class="form-group">
                        <?php echo Form::label('title', '名称', [
                            'class' => 'col-sm-3 control-label'
                        ]); ?>

                        <div class="col-sm-6">
                            <?php echo Form::text('title', null, [
                                'class' => 'form-control',
                                'placeholder' => '(不超过40个汉字)',
                                'required' => 'true',
                                'data-parsley-length' => '[1, 40]'
                            ]); ?>

                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo Form::label('remark', '备注',['class' => 'col-sm-3 control-label']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::text('remark', null, [
                            'class' => 'form-control',
                            'placeholder' => '(不超过40个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[1, 40]'
                            ]); ?>

                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo Form::label('location', '地点',['class' => 'col-sm-3 control-label']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::text('location', null, [
                            'class' => 'form-control',
                            'placeholder' => '(不超过40个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[1, 40]'
                            ]); ?>

                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo Form::label('contact', '联系人',['class' => 'col-sm-3 control-label']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::text('contact', null, [
                            'class' => 'form-control',
                            'required' => 'true'
                            ]); ?>

                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo Form::label('url', '事件URL',['class' => 'col-sm-3 control-label']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::text('url', null, [
                            'class' => 'form-control',
                            'data-parsley-type' => "url"
                            ]); ?>

                        </div>
                    </div>
                    <div class="form-group ispublic-form">
                        <?php echo Form::label('ispublic', '是否公开',['class' => 'col-sm-3 control-label']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::radio('ispublic', '1'); ?>

                            <?php echo Form::label('ispublic', '是'); ?>

                            <?php echo Form::radio('ispublic', '0', true); ?>

                            <?php echo Form::label('ispublic', '否'); ?>

                        </div>
                    </div>
                    <div class="form-group iscourse-form" style="display:none">
                        <?php echo Form::label('iscourse', '是否为课程事件',['class' => 'col-sm-3 control-label']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::radio('iscourse', '1'); ?>

                            <?php echo Form::label('iscourse', '是'); ?>

                            <?php echo Form::radio('iscourse', '0',true); ?>

                            <?php echo Form::label('iscourse', '否'); ?>

                        </div>
                    </div>
                    <div class="form-group educator_id-form" style="display:none">
                        <?php echo Form::label('educator_id', '教师姓名',['class' => 'col-sm-3 control-label']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::select('educator_id', $educators, null, [ 'class' => 'form-control']); ?>

                        </div>
                    </div>
                    <div class="form-group subject_id-form" style="display:none">
                        <?php echo Form::label('subject_id', '科目名称',['class' => 'col-sm-3 control-label']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::select('subject_id', $subjects, null, [ 'class' => 'form-control']); ?>

                        </div>
                    </div>
                    <div class="form-group alertable-form">
                        <?php echo Form::label('alertable', '是否设置提醒',['class' => 'col-sm-3 control-label']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::radio('alertable', '1'); ?>

                            <?php echo Form::label('alertable', '是'); ?>

                            <?php echo Form::radio('alertable', '0', true); ?>

                            <?php echo Form::label('alertable', '否'); ?>

                        </div>
                    </div>
                    <div class="form-group alert_mins" style="display:none">
                        <?php echo Form::label('alert_mins', '提醒时间',['class' => 'col-sm-3 control-label']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::text('alert_mins', null, [ 'class' => 'form-control']); ?>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn  btn-default" data-dismiss="modal">取消</a>
                    <a id="confirm-create" href="#" class="btn btn-primary" data-dismiss="modal">确定</a>
                </div>
                <?php echo Form::close(); ?>

            </div><!-- /.modal-content -->
        </div>
    </div><!-- /.modal -->
</div>