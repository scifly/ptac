<div class="modal fade" id="modal-create-event">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    设置日程具体时间
                </h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <?php echo Form::label('start', '开始时间',['class' => 'col-sm-3 control-label ']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::text('start', null, [ 'class' => 'form-control start-datepicker']); ?>

                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo Form::label('end', '结束时间',['class' => 'col-sm-3 control-label']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::text('end', null, ['class' => 'form-control end-datepicker']); ?>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn  btn-default" data-dismiss="modal">取消</a>
                    <a id="confirm-add-time" href="#" class="btn btn-primary" data-dismiss="modal">确定</a>
                </div>
            </div><!-- /.modal-content -->
        </div>
    </div><!-- /.modal -->
</div>
