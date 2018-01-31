<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.list_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
			<tr class="bg-info">
                <th>#</th>
                <th>学生姓名</th>
                <th>学生卡号</th>
                <th>打卡时间</th>
                <th>进或出</th>
                <th>考勤机</th>
                <th>经度</th>
                <th>纬度</th>
                <th>创建时间</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <?php echo $__env->make('partials.form_overlay', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>

