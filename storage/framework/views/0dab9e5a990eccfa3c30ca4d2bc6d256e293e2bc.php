<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.list_header', ['addBtn' => false], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <?php echo Form::open([
            'method' => 'post',
            'id' => 'formApp',
            'data-parsley-validate' => 'true'
        ]); ?>

        <div class="form-inline">
            <!-- 所属企业 -->
            <div class="form-group" style="margin-right: 10px">
                <?php echo Form::label('corp_id', '所属企业：', [
                    'class' => 'control-label',
                ]); ?>

                <?php echo Form::label('name', $corp->name, [
                    'class' => 'control-label',
                    'style' => 'font-weight: normal;'
                ]); ?>

            </div>
            <!-- 企业应用ID -->
            <div class="form-group" style="margin-right: 10px">
                <?php echo Form::label('agentid', '应用AgentId：', [
                    'class' => 'control-label'
                ]); ?>

                <?php echo Form::text('agentid', null, [
                    'id' => 'agentid',
                    'class' => 'form-control input-sm',
                    'required' => 'true',
                ]); ?>

            </div>
            <!-- 应用Secret -->
            <div class="form-group" style="margin-right: 10px">
                <?php echo Form::label('secret', '应用Secret：', [
                    'class' => 'control-label'
                ]); ?>

                <?php echo Form::text('secret', null, [
                    'id' => 'secret',
                    'class' => 'form-control input-sm',
                    'required' => 'true',
                    'data-parsley-length' => '[43,43]'
                ]); ?>

            </div>
            <?php echo Form::submit('同步应用', [
                'id' => 'sync',
                'class' => 'btn btn-default'
            ]); ?>

        </div>
        <?php echo Form::close(); ?>

        <!-- 企业应用列表 -->
        <div style="display: block; overflow-x: auto; clear: both; width: 100%; margin-top: 10px;">
            <table class="table-striped table-bordered table-hover table-condensed"
               style="white-space: nowrap; width: 100%;">
            <thead>
			<tr class="bg-info">
                <th>#</th>
                <th class="text-center">应用id</th>
                <th class="text-center">应用名称</th>
                <th class="text-center">应用头像</th>
                <th>应用详情</th>
                <th class="text-center">创建于</th>
                <th class="text-center">更新于</th>
                <th class="text-right">状态</th>
            </tr>
            </thead>
            <tbody>
            <?php if(sizeof($apps) == 0): ?>
                <tr id="na">
                    <td colspan="8" style="text-align: center">( n/a )</td>
                </tr>
            <?php else: ?>
                <?php $__currentLoopData = $apps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr id="app<?php echo e($app['agentid']); ?>">
                        <td><?php echo e($app['id']); ?></td>
                        <td class="text-center"><?php echo e($app['agentid']); ?></td>
                        <td class="text-center"><?php echo e($app['name']); ?></td>
                        <td class="text-center"><img style="width: 16px; height: 16px;" src="<?php echo e($app['square_logo_url']); ?>"/></td>
                        <td><?php echo e($app['description']); ?></td>
                        <td class="text-center"><?php echo e($app['created_at']); ?></td>
                        <td class="text-center"><?php echo e($app['updated_at']); ?></td>
                        <td class="text-right">
                            <?php if($app['enabled']): ?>
                                <i class="fa fa-circle text-green" title="已启用"></i>
                            <?php else: ?>
                                <i class="fa fa-circle text-gray" title="未启用"></i>
                            <?php endif; ?>
                            &nbsp;&nbsp;
                            <a href="#"><i class="fa fa-pencil" title="修改"></i></a>
                            &nbsp;&nbsp;
                            <a href="#"><i class="fa fa-exchange" title="同步菜单"></i></a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php echo $__env->make('partials.form_overlay', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>