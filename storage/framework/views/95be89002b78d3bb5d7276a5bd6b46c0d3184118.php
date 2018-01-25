<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-default box-solid">
                    <div class="box-header with-border">
                        <h4 class="box-title">Draggable Events</h4>
                    </div>
                    <div class="box box-primary" style="position: relative; left: 0px; top: 0px;">
                        <div class="box-body">
                            <?php if(!empty($userId)): ?>
                                <input hidden name="user_id" value=<?php echo e($userId); ?>>
                            <?php endif; ?>
                            <input hidden name="isAdmin" value=<?php echo e($isAdmin); ?>>
                            <ul id="external-events" class="todo-list ui-sortable" style="overflow: visible">
                                <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="external-event" style="padding: 5px">
                                        <span id=<?php echo e($event['id']); ?> class="text"><?php echo e($event['title']); ?></span>
                                        <div class="tools">
                                            
                                            <i class="fa fa-trash-o trash-list"></i>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                        <div class="box-footer clearfix no-border">
                            <button id="add-new-event" type="button" class="btn btn-default pull-right">Create Event
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-body no-padding">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php echo $__env->make('event.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('event.create', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('event.edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>