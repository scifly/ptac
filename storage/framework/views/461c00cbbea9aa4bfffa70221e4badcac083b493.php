<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo e(Form::hidden('export-type', 'custodian')); ?>

        <?php echo $__env->make('partials.list_header', [
            'buttons' => [
                'export' => [
                    'id' => 'export',
                    'label' => '批量导出',
                    'icon' => 'fa fa-arrow-circle-down'
                ]
            ]
        ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
			<tr class="bg-info">
                <th>#</th>
                <th>监护人姓名</th>
                <th>学生姓名</th>
                <th>性别</th>
                <th>手机号码</th>
                <th>创建于</th>
                <th>更新于</th>
                <th>状态</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <?php echo $__env->make('partials.form_overlay', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
