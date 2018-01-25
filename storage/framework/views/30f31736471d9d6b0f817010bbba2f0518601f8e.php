<div class="form-group">
    <label for="<?php echo e($id); ?>" class="col-sm-3 control-label">
        <?php echo e($label); ?>

    </label>
    <div class="col-sm-6">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="<?php if(isset($icon)): ?> <?php echo e($icon); ?> <?php else: ?> fa fa-list-alt <?php endif; ?>"></i>
            </div>
            <select multiple="multiple" name="<?php echo e($id); ?>[]" id="<?php echo e($id); ?>" class='form-control select2' style="width: 100%;">
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(isset($selectedItems)): ?>
                        <option value="<?php echo e($key); ?>"
                                <?php if(array_key_exists($key, $selectedItems)): ?>
                                selected
                                <?php endif; ?>
                        >
                            <?php echo e($value); ?>

                        </option>
                    <?php else: ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>
</div>