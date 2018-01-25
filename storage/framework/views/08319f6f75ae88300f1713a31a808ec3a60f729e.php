

<?php $__env->startSection('css'); ?>
	<link rel="stylesheet" href="<?php echo e(asset('css/wechat/score/detail.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
	<div class="header">
		<div class="title">
			<?php echo e($data['exam']); ?>

		</div>

		<div class="myclass">
			<?php echo e($data['exam']); ?>

		</div>
	</div>
	<div class="weui-search-bar" id="searchBar">
		<form class="weui-search-bar__form" action="">
			<div class="weui-search-bar__box">
				<i class="weui-icon-search"></i>
				<input class="weui-search-bar__input" name="student" id="searchInput" placeholder="搜索" required="">
				<a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
			</div>
			<label class="weui-search-bar__label" id="searchText" style="transform-origin: 0px 0px 0px; opacity: 1; transform: scale(1, 1);">
				<i class="weui-icon-search"></i>
				<span>搜索</span>
			</label>
		</form>
		<a href="javascript:" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
	</div>

	<div class="main">
		<table class="tongji-table" style="width: 100%;" cellspacing="0">
			<thead>
			<tr>
				<td width="40">姓名</td>
				<td width="40">学号</td>
				<td width="40">班排</td>
				<td width="40">年排</td>
				<td width="40">总分</td>
				<td width="80">成绩详情</td>
			</tr>
			</thead>

			<tbody>
			<?php if($data['items']): ?>
				<?php $__currentLoopData = $data['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

					<tr class="tongji-item" data-s="<?php echo e($d['student_id']); ?>" data-e="<?php echo e($d['exam_id']); ?>">
						<td><?php echo e($d['realname']); ?></td>
						<td><?php echo e($d['student_number']); ?></td>
						<td><?php echo e($d['class_rank']); ?></td>
						<td><?php echo e($d['grade_rank']); ?></td>
						<td><?php echo e($d['total']); ?></td>
						<td>
							<?php if($d['detail']): ?>
								<?php $__currentLoopData = $d['detail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

									<div>
										<span class="subj"><?php echo e($t['subject']); ?></span>
										<span class="score"><?php echo e($t['score']); ?></span>
										<div style="clear: both;"></div>
									</div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>

						</td>

					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			<?php endif; ?>
			</tbody>
		</table>
	</div>
	<div style="height: 70px;width: 100%;"></div>

	<div class="footerTab" >
		<a class="btnItem footer-active">
			<i class="icon iconfont icon-document"></i>
			<p>详情</p>
		</a>
		<a class="btnItem" href='<?php echo e(url("wechat/score/analysis?examId=". $examId ."&classId=". $classId)); ?>'>
                            <i class="icon iconfont icon-renzheng7"></i>
                            <p>统计</p>
                        </a>
                        <div style="clear: both;"></div>
                    </div>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
	<script>
        $('.tongji-item').click(function(){
            var student = $(this).attr('data-s');
            var exam = $(this).attr('data-e');
            window.location.href = '../score/show?student='+student+'&exam='+exam;
        });

	</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('wechat.layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>