<?php echo Form::model($educator, ['url' => '/educators/' . $educator->id, 'method' => 'put', 'id' => 'formEducator', 'data-parsley-validate' => 'true']); ?>

<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($educator['id'])): ?>
                <?php echo e(Form::hidden('id', $educator['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('user_id', '充值用户', [
                    'class' => 'col-sm-3 control-label',
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::select('user_id', $users, null, [
                        'class' => 'form-control',
                        'style' => 'width: 100%;',
                        'disabled' => 'disabled'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('sms_quote', '余额', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('sms_quote', null, [
                        'class' => 'form-control',
                        'disabled' => 'disabled'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('recharge', '充值条数', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('recharge', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入充值条数)',
                        'required' => 'true',
                        'maxlength' => '255'
                    ]); ?>

                </div>
            </div>

        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<?php echo Form::close(); ?>

