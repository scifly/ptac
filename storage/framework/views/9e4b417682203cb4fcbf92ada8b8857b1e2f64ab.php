<div class="form-group">
    <label class="col-sm-3 control-label">班级科目关系</label>
    <div class="col-sm-6">
        <table id="classes" class="table-bordered table-responsive" style="width: 100%;">
            <thead>
			<tr class="bg-info">
                <td class="text-center">班级</td>
                <td class="text-center">科目</td>
                <td class="text-center">+/-</td>
            </tr>
            </thead>
            <tbody>

            <?php if(isset($educator->educatorClasses) && count($educator->educatorClasses) !=0 ): ?>
                <?php $__currentLoopData = $educator->educatorClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=> $educatorClass): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="text-left">
                            <label for="classSubject[class_ids][]"></label>
                            <select name="classSubject[class_ids][]"
                                    id="classSubject[class_ids][]"
                                    class="select2"
                                    style="width: 98%;"
                            >
                                <?php $__currentLoopData = $squads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $squad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value='<?php echo e($key); ?>'
                                            <?php if($key == $educatorClass->class_id): ?> selected <?php endif; ?>><?php echo e($squad); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="text-left">
                            <label for="classSubject[subject_ids][]"></label>
                            <select name="classSubject[subject_ids][]"
                                    id="classSubject[subject_ids][]"
                                    class="select2"
                                    style="width: 98%;"
                            >
                                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value='<?php echo e($key); ?>'
                                            <?php if($key == $educatorClass->subject_id): ?> selected <?php endif; ?>><?php echo e($subject); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </td>
                        <td class="text-center">
                            <?php if($index == sizeof($educator->$educatorClass) - 1): ?>
                                <span class="input-group-btn">
                                            <button class="btn btn-box-tool  btn-class-add btn-add" type="button">
                                                <i class="fa fa-plus text-blue"></i>
                                            </button>
                                        </span>
                            <?php else: ?>
                                <span class="input-group-btn">
                                            <button class="btn btn-box-tool  btn-class-remove btn-remove" type="button">
                                                <i class="fa fa-minus text-blue"></i>
                                            </button>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <tr>
                    <td class="text-left">
                    <label for="classSubject[class_ids][]"></label>
                        <select name="classSubject[class_ids][]"
                                id="classSubject[class_ids][]"
                                class="select2"
                                style="width: 98%;"
                        >
                            <?php $__currentLoopData = $squads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $squad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value='<?php echo e($key); ?>'><?php echo e($squad); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                    <td class="text-left">
                        <label for="classSubject[subject_ids][]"></label>
                        <select name="classSubject[subject_ids][]"
                                id="classSubject[subject_ids][]"
                                class="select2"
                                style="width: 98%;"
                        >
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value='<?php echo e($key); ?>'><?php echo e($subject); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                    <td class="text-center">
                        <span class="input-group-btn">
                            <button class="btn btn-box-tool btn-class-add" type="button">
                                <i class="fa fa-plus text-blue"></i>
                            </button>
                        </span>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
