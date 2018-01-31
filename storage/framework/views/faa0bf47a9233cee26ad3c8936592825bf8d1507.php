<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.list_header', [
            'buttons' => [
                'import' => [
                    'id' => 'import',
                    'label' => '批量导入',
                    'icon' => 'fa fa-arrow-circle-up',
                ],
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
                <th>姓名</th>
                <th>性别</th>
                <th>班级</th>
                <th>学号</th>
                <th>卡号</th>
                <th>住校</th>
                <th>手机</th>
                <th>生日</th>
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

<!-- 导入excel -->
<form class='import' method='post' enctype='multipart/form-data' id="form-import">
    <?php echo e(csrf_field()); ?>

    <div class="modal fade" id="import-pupils">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">批量导入</h4>
                </div>
                <div class="modal-body with-border">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <?php echo e(Form::label('import', '选择导入文件', [
                                'class' => 'control-label col-sm-3'
                            ])); ?>


                            <div class="col-sm-6">
                                <input type="file" id="fileupload" accept=".xls,.xlsx" name="file">
                                <p class="help-block">下载<a href="<?php echo e(URL::asset('files/students.xlsx')); ?>">模板</a></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
                    <a id="confirm-import" href="#" class="btn btn-sm btn-success" data-dismiss="modal">确定</a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- 导出excel -->
<div class="modal fade" id="export-pupils">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">导出</h4>
            </div>
            <div class="modal-body with-border">
                <div class="form-horizontal">
                    <!-- 所属学校 -->
                    <div class="form-group">
                        <?php if(isset($schools)): ?>
                            <?php echo $__env->make('partials.single_select', [
                                    'id' => 'schoolId',
                                    'label' => '所属学校',
                                    'items' => $schools
                                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php endif; ?>
                    </div>
                    <!-- 所属年级 -->
                    <div class="form-group">
                        <?php if(isset($grades)): ?>
                            <?php echo $__env->make('partials.single_select', [
                                    'id' => 'gradeId',
                                    'label' => '所属年级',
                                    'items' => $grades
                                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                        <?php endif; ?>
                    </div>
                    <!-- 所属班级 -->
                    <div class="form-group">
                        <?php if(isset($classes)): ?>
                            <?php echo $__env->make('partials.single_select', [
                                    'id' => 'classId',
                                    'label' => '所属班级',
                                    'items' => $classes
                                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
                <a id="confirm-bind" href="javascript:" class="btn btn-sm btn-success">确定</a>
            </div>
        </div>
    </div>
</div>
