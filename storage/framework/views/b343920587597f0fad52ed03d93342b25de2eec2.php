<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.list_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%"
               class="display nowrap dataTable table table-striped table-hover table-bordered">
            <thead>
			<tr class="bg-info">
                <th>#</th>
                <th>名称</th>
                <th>地址</th>
                <th>类型</th>
                <th>所属企业</th>
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