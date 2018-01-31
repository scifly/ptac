<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li><a href="#tab03" data-toggle="tab">卡片/功能权限</a></li>
                <li><a href="#tab02" data-toggle="tab">菜单权限</a></li>
                <li class="active"><a href="#tab01" data-toggle="tab">基本信息</a></li>
                <li class="pull-left header"><i class="fa fa-th"></i>角色</li>
            </ul>
            <div class="tab-content">
                <!-- 角色基本信息 -->
                <div class="tab-pane active" id="tab01">
                    <div class="form-horizontal">
                        <!-- 角色ID -->
                        <?php if(!empty($group['id'])): ?>
                            <?php echo e(Form::hidden('id', $group['id'], ['id' => 'id'])); ?>

                        <?php endif; ?>
                        <?php echo e(Form::hidden('menu_ids', isset($selectedMenuIds) ? $selectedMenuIds : null, [
                            'id' => 'menu_ids'
                        ])); ?>

                        <!-- 角色名称 -->
                        <div class="form-group">
                            <?php echo Form::label('name', '名称', [
                                'class' => 'col-sm-3 control-label'
                            ]); ?>

                            <div class="col-sm-6">
                                <?php echo Form::text('name', null, [
                                    'class' => 'form-control',
                                    'placeholder' => '(不得超过20个汉字)',
                                    'required' => 'true',
                                    'data-parsley-length' => '[2, 20]'
                                ]); ?>

                            </div>
                        </div>
                        <!-- 角色所属学校 -->
                        <div class="form-group">
                            <label for="school_id" class="col-sm-3 control-label">所属学校</label>
                            <div class="col-sm-6">
                                <?php if(!isset($group)): ?>
                                    <select name="school_id" class="form-control menu" id="school_id" style="width: 100%">
                                        <?php $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" ><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                <?php else: ?>
                                    <?php echo Form::hidden('school_id', $group['school_id'], ['id' => 'school_id']); ?>

                                    <label class="control-label" style="font-weight: normal;"><?php echo $group->school->name; ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- 角色备注 -->
                        <?php echo $__env->make('partials.remark', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <!-- 状态 -->
                        <?php echo $__env->make('partials.enabled', [
                            'id' => 'enabled',
                            'value' => $group['enabled'] ?? null
                        ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                </div>
                <!-- 角色菜单权限 -->
                <div class="tab-pane" id="tab02">
                    <div id="menu_tree" class="form-inline"></div>
                </div>
                <!-- 角色卡片/功能权限 -->
                <div class="tab-pane" id="tab03">
                    <div class="row">
                    <?php $__currentLoopData = $tabActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabAction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3">
                            <div class="box box-default collapsed-box">
                                <div class="box-header with-border">
                                    <label for="tabs[<?php echo e($tabAction['tab']['id']); ?>]['enabled']" class="tabsgroup">
                                        <input name="tabs[<?php echo e($tabAction['tab']['id']); ?>]['enabled']"
                                               id="tabs[]" type="checkbox" class="minimal tabs"
                                               <?php if(isset($selectedTabs) && in_array($tabAction['tab']['id'], $selectedTabs)): ?>
                                                   checked
                                               <?php endif; ?>
                                        >&nbsp;<span style="margin-left: 5px;"><?php echo e($tabAction['tab']['name']); ?></span>
                                    </label>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <ul class="nav nav-stacked">
                                        <?php $__currentLoopData = $tabAction['actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <p class="help-block">
                                                    <label for="actions[<?php echo e($action['id']); ?>]['enabled']"></label>
                                                    <input name="actions[<?php echo e($action['id']); ?>]['enabled']"
                                                           id="actions[<?php echo e($action['id']); ?>]['enabled']"
                                                           type="checkbox" class="minimal actions"
                                                           data-method="<?php echo e($action['method']); ?>"
                                                           <?php if(isset($selectedActions) && in_array($action['id'], $selectedActions)): ?>
                                                               checked
                                                           <?php endif; ?>
                                                    >&nbsp;<span><?php echo e($action['name']); ?></span>
                                                </p>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>