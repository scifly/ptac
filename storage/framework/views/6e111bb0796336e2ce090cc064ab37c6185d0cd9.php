<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.list_header', ['addBtn' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        
        <div id="tree" class="col-md-12"></div>
    </div>
    <?php echo $__env->make('partials.form_overlay', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>