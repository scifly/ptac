<link rel="stylesheet" href="<?php echo e(URL::asset('css/bootstrap.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(URL::asset('js/plugins/parsley/parsley.css')); ?>">
<link rel="stylesheet" href="<?php echo e(URL::asset('js/plugins/datatables/datatables.min.css')); ?>">
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<section class="content clearfix">
    <?php echo $__env->make('partials.modal_dialog', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
            <div class="box box-default box-solid">
                <div class="box-header with-border">
                    <span id="breadcrumb" style="color: #999; font-size: 13px;">用户中心/信息列表</span>
                </div>
                <div class="box-body">
                    <table id="data-table" style="width: 100%"
                           class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr class="bg-info">
                            <th>#</th>
                            <th>通信方式</th>
                            <th>应用</th>
                            <th>消息批次</th>
                            <th>发送者</th>
                            <th>类型</th>
                            <th>已读</th>
                            <th>已发</th>
                            <th>创建于</th>
                            <th>更新于</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <?php echo $__env->make('partials.form_overlay', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        </div>
    </div>
</section>
<script src="<?php echo e(URL::asset('js/jquery.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/plugins/parsley/parsley.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/plugins/parsley/i18n/zh_cn.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/plugins/parsley/i18n/zh_cn.extra.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/plugins/datatables/datatables.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/plugins/datatables/dataTables.checkboxes.min.js')); ?>"></script>
<?php if(isset($messages)): ?>
    <script src="<?php echo e(URL::asset($messages)); ?>"></script>
<?php endif; ?>
