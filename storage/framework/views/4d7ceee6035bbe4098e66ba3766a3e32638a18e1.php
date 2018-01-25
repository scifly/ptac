<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($menu['id'])): ?>
                <?php echo e(Form::hidden('id', $menu['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <?php if(!empty($menu['position'])): ?>
                <?php echo e(Form::hidden('position', $menu['position'], ['id' => 'position'])); ?>

            <?php endif; ?>
            <?php echo e(Form::hidden('parent_id', $parentId ?? null, ['id' => 'parent_id'])); ?>

            <!-- 名称 -->
            <div class="form-group">
                <?php echo Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-list-ul"></i>
                        </div>
                        <?php echo Form::text('name', null, [
                            'id' => 'name',
                            'class' => 'form-control',
                            'placeholder' => '(不得超过8个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 8]'
                        ]); ?>

                    </div>
                </div>
            </div>
            <!-- 菜单类型ID -->
            <?php echo e(Form::hidden('menu_type_id', $menuTypeId ?? null, [
                'id' => 'menu_type_id'
            ])); ?>

            <!-- 图标ID -->
            <div class="form-group">
                <?php echo Form::label('icon_id', '图标', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-fonticons"></i>
                        </div>
                        <?php echo e(Form::select('icon_id', $icons, null, [
                            'id' => 'icon_id',
                            'style' => 'width: 100%;'
                        ])); ?>

                    </div>
                </div>
            </div>
            <!-- 链接地址 -->
            <div class="form-group">
                <?php echo Form::label('name', '链接地址', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-link"></i>
                        </div>
                        <?php echo Form::text('uri', null, [
                            'id' => 'uri',
                            'class' => 'form-control',
                            'placeholder' => '(可选)',
                            'data-parsley-length' => '[1, 255]'
                        ]); ?>

                    </div>
                </div>
            </div>
            <!-- 包含的卡片 -->
            <?php echo $__env->make('partials.multiple_select', [
                'label' => '包含卡片',
                'id' => 'tab_ids',
                'icon' => 'fa fa-calendar-check-o',
                'items' => $tabs,
                'selectedItems' => $selectedTabs ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <!-- 备注 -->
            <?php echo $__env->make('partials.remark', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <!-- 状态 -->
            <?php echo $__env->make('partials.enabled', [
                'id' => 'enabled',
                'value' => $menu['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>