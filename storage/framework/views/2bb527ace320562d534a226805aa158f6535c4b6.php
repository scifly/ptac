
<?php $__env->startSection('css'); ?>
	<link rel="stylesheet" href="<?php echo e(asset('css/wechat/score/student_score.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
	<div class="header">
		<div class="title">
			学生：<?php echo e($student->user->realname); ?>

		</div>
		<div class="myclass">
			<?php echo e($student->squad->name); ?>

		</div>
		<input type="hidden" value="<?php echo e($student->id); ?>" id="student_id">
		<input type="hidden" value="<?php echo e($exam->id); ?>" id="exam_id">
	</div>
	<div class="tab-bar">
		<div class="tab-item active">
			总分
			<input type="hidden" value="-1" >
		</div>
		<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<div class="tab-item">
				<?php echo e($d->name); ?>

				<input type="hidden" value="<?php echo e($d->id); ?>" >
			</div>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</div>

	<div class="line-table-con class-rank">

	</div>

	<div class="line-table-con grade-rank">

	</div>

	<div style="height: 70px;width: 100%;"></div>
	<div class="footerTab" >
		<a class="btnItem footer-active">
			<i class="icon iconfont icon-document"></i>
			<p>详情</p>
		</a>
		<a class="btnItem">
			<i class="icon iconfont icon-renzheng7"></i>
			<p>统计</p>
		</a>
		<div style="clear: both;"></div>
	</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
	<script src="<?php echo e(asset('/js/wechat/score/show.js')); ?>"></script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('wechat.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>