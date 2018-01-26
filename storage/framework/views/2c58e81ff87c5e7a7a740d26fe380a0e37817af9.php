<?php $lblStyle = "margin: 0 5px; vertical-align: middle; font-weight: normal;"; ?>
<div class="form-group">
    <label for="<?php echo e($id); ?>" class="col-sm-3 control-label">
        <?php if(isset($label)): ?> <?php echo e($label); ?> <?php else: ?> 状态 <?php endif; ?>
    </label>
    <div class="col-sm-6" style="padding-top: 5px;">
        <input id="<?php echo e($id); ?>1" <?php if($value): ?> checked <?php endif; ?>
               type="radio" name="<?php echo e($id); ?>" class="minimal" value="1">
        <label for="<?php echo e($id); ?>1" style="<?php echo $lblStyle; ?>">
            <?php if(isset($options)): ?> <?php echo e($options[0]); ?> <?php else: ?> 启用 <?php endif; ?>
        </label>
        <input id="<?php echo e($id); ?>2" <?php if(!$value): ?> checked <?php endif; ?>
               type="radio" name="<?php echo e($id); ?>" class="minimal" value="0">
        <label for="<?php echo e($id); ?>2" style="<?php echo $lblStyle; ?>">
            <?php if(isset($options)): ?> <?php echo e($options[1]); ?> <?php else: ?> 禁用 <?php endif; ?>
        </label>
    </div>
</div>