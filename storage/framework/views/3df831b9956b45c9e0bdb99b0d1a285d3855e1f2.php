<style>.parsley-errors-list.filled{text-align: left}</style>
<div class="form-group">
    <label for="mobile" class="col-sm-3 control-label">手机</label>
    <div class="col-sm-6">
        <div style="display: block; overflow-x: auto; clear: both; width: 100%; margin-top: 10px;">
            <table id="mobiles" class="table-bordered table-responsive"
                   style="white-space: nowrap; width: 100%;">
                <thead>
                <tr class="bg-info">
                    <td class="text-center">号码</td>
                    <td class="text-center">默认</td>
                    <td class="text-center">启用</td>
                    <td class="text-center">+/-</td>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($mobiles) && sizeof($mobiles) != 0): ?>
                    <?php $__currentLoopData = $mobiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $mobile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-center">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-mobile"></i>
                                    </div>
                                    <input class="form-control"
                                           name="mobile[<?php echo e($key); ?>][mobile]"
                                           placeholder="（请输入手机号码）"
                                           value='<?php echo e($mobile->mobile); ?>'
                                           required
                                           pattern="/^1[0-9]{10}$/"
                                           style="width: 75%"
                                    />
                                    <input class="form-control"
                                           name="mobile[<?php echo e($key); ?>][id]"
                                           type="hidden"
                                           value='<?php echo e($mobile->id); ?>'
                                    />

                                </div>
                            </td>
                            <td class="text-center">
                                <label for="mobile[isdefault]"></label>
                                <input name="mobile[isdefault]"
                                       value="<?php echo e($key); ?>"
                                       id="mobile[isdefault]"
                                       type="radio"
                                       class="minimal"
                                       required
                                       <?php if($mobile->isdefault): ?> checked <?php endif; ?>
                                />
                            </td>
                            <td class="text-center">
                                <label for="mobile[<?php echo e($key); ?>][enabled]"></label>
                                <input name="mobile[<?php echo e($key); ?>][enabled]"
                                       value="<?php echo e($mobile->enabled); ?>"
                                       id="mobile[<?php echo e($key); ?>][enabled]"
                                       type="checkbox"
                                       class="minimal"
                                       <?php if($mobile->enabled): ?> checked <?php endif; ?>
                                />
                            </td>
                            <td class="text-center">
                                <?php if($key == sizeof($mobiles) - 1): ?>
                                    <span class="input-group-btn">
                                        <button class="btn btn-box-tool btn-add btn-mobile-add" type="button">
                                            <i class="fa fa-plus text-blue" title="新增"></i>
                                        </button>
                                    </span>
                                <?php else: ?>
                                    <span class="input-group-btn">
                                        <button class="btn btn-box-tool btn-remove btn-mobile-remove" type="button">
                                            <i class="fa fa-minus text-blue" title="删除"></i>
                                        </button>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <!-- 手机号码数量 -->
                    <input class="form-control" type="hidden" id="count" value=<?php echo e(sizeof($mobiles)); ?>>
                <?php else: ?>
                    <tr>
                        <td class="text-center">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-mobile"></i>
                                </div>
                                <input class="form-control"
                                       name="mobile[0][mobile]"
                                       placeholder="（请输入手机号码）"
                                       value=''
                                       required
                                       pattern="/^1[0-9]{10}$/"
                                       style="width: 75%"
                                />
                            </div>
                        </td>
                        <td class="text-center">
                            <label for="mobile[isdefault]"></label>
                            <input id="mobile[isdefault]"
                                   name="mobile[isdefault]"
                                   value="0"
                                   checked
                                   type="radio"
                                   class="minimal"
                            />
                        </td>
                        <td class="text-center">
                            <label for="mobile[0][enabled]"></label>
                            <input id="mobile[0][enabled]"
                                   name="mobile[0][enabled]"
                                   checked
                                   type="checkbox"
                                   class="minimal"
                            />
                        </td>
                        <td class="text-center">
                            <span class="input-group-btn">
                                <button class="btn btn-box-tool btn-add btn-mobile-add" type="button">
                                    <i class="fa fa-plus text-blue" title="新增"></i>
                                </button>
                            </span>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>