
<?php $__env->startSection('title'); ?>
    <title>成绩中心</title>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/wechat/score/analysis.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="header">
        <div class="title">
            <?php echo e($data['examName']); ?>

        </div>
        <div class="time">
            <?php echo e($data['className']); ?>

        </div>
    </div>
    <div class="main" style="width: 92%;padding: 0 4%;">
        <?php if(!empty($data['oneData'])): ?>
            <?php $__currentLoopData = $data['oneData']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $one): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="subjectItem" id="lie-<?php echo e($one['subId']); ?>">
                    <div class="subj-title">
                        <?php echo e($one['sub']); ?>

                    </div>
                    <div class="subj-tab">
                        <a class="tab-item cur" data-type="score">分数统计</a>
                        <a class="tab-item" data-type="score-level">分数段统计</a>
                        <a class="tab-item" data-type="table">图表统计</a>
                    </div>
                    <div class="subj-main">
                        <div class="show-item score cur">
                            <div class="table-title"><?php echo e($one['sub']); ?>分数统计详情</div>
                            <table class="table-count">
                                <tr>
                                    <td class="subtit" width="">统计人数</td>
                                    <td><?php echo e($one['count']); ?></td>
                                </tr>
                                <tr>
                                    <td class="subtit">最高分</td>
                                    <td><?php echo e($one['max']); ?></td>
                                </tr>
                                <tr>
                                    <td class="subtit">最低分</td>
                                    <td><?php echo e($one['min']); ?></td>
                                </tr>
                                <tr>
                                    <td class="subtit">平均分</td>
                                    <td><?php echo e($one['avg']); ?></td>
                                </tr>
                                <tr>
                                    <td class="subtit">平均分以上人数</td>
                                    <td><?php echo e($one['big_number']); ?></td>
                                </tr>
                                <tr>
                                    <td class="subtit">平均分以下人数</td>
                                    <td><?php echo e($one['min_number']); ?></td>
                                </tr>
                            </table>
                        </div>

                        <div class="show-item score-level">
                            <div class="table-title"><?php echo e($one['sub']); ?>分数统计详情</div>
                            <table class="table-count">
                                <tr>
                                    <td class="subtit">统计人数</td>
                                    <td><?php echo e($data['rangs'][$one['subId']][0]['score']['count']); ?></td>
                                </tr>
                                <?php $__currentLoopData = $data['rangs'][$one['subId']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="subtit"><?php echo e($ran['range']['min']); ?> - <?php echo e($ran['range']['max']); ?>分</td>
                                        <td><?php echo e($ran['score']['number']); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </table>
                        </div>
                        <div class="show-item table">
                            <div id="main"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <div>
                <p style="text-align: center;">本场考试未录入本班数据！</p>
            </div>
        <?php endif; ?>
    </div>
    <div style="height: 70px;width: 100%;"></div>
    <div class="anchor-point">
        <ul>
            <?php if(!empty($data['oneData'])): ?>
                <?php $__currentLoopData = $data['oneData']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $datum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a href="#lie-<?php echo e($datum['subId']); ?>"><?php echo e($datum['sub']); ?></a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </ul>
    </div>
    <div class="footerTab">
        <a class="btnItem" href='<?php echo e(url("wechat/score/detail?examId=". $examId ."&classId=". $classId)); ?>'>
            <i class="icon iconfont icon-document"></i>
            <p>详情</p>
        </a>
        <a class="btnItem footer-active">
            <i class="icon iconfont icon-renzheng7"></i>
            <p>统计</p>
        </a>
        <div style="clear: both;"></div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(asset('js/wechat/score/analysis.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('wechat.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>