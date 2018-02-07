	<div class="row">
			<div class="title">
				<?php echo e($className); ?>·班级成绩分析
			</div>
	        <div class="box-tools pull-right">
	            <i class="fa fa-close " id="close-data"></i>
	        </div>
		</div>
		<div class="row">
			<div class="subtitle">
				<?php echo e($examName); ?>

			</div>
	        <table id="score-count" style="width: 100%;"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
	            <thead>
					<tr class="bg-info">
		                <th>科目</th>
		                <th>统计人数</th>
		                <th>最高分</th>
		                <th>最低分</th>
		                <th>平均分</th>
		                <th>平均分以上</th>
		                <th>平均分以下</th>
		            </tr>
	            </thead>
	            <tbody>
					<?php $__currentLoopData = $oneData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $one): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td><?php echo e($one['sub']); ?></td>
						<td><?php echo e($one['count']); ?></td>
						<td><?php echo e($one['max']); ?></td>
						<td><?php echo e($one['min']); ?></td>
						<td><?php echo e($one['avg']); ?></td>
						<td><?php echo e($one['big_number']); ?></td>
						<td><?php echo e($one['min_number']); ?></td>
					</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	            </tbody>
	        </table>
		</div>

	<?php if(!empty($rangs)): ?>
		<div class="row">
			<div class="subtitle">
				各科分数段成绩分布情况
			</div>
	        <table id="score-level" style="width: 100%;"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
				<?php $__currentLoopData = $rangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	             <thead>
					<tr class="bg-info">
		                <th>科目</th>
		                <th>统计人数</th>
						<?php $__currentLoopData = $ran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		                <th><?php echo e($r['range']['min']); ?>-<?php echo e($r['range']['max']); ?> 分</th>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tr>
	            </thead>
	            <tbody>
				<tr>
					<td><?php echo e($ran[0]['score']['sub']); ?>(人)</td>
					<td><?php echo e($ran[0]['score']['count']); ?></td>
					<?php $__currentLoopData = $ran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<td><?php echo e($rs['score']['number']); ?> </td>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tr>
				</tbody>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	        </table>
		</div>
	<?php endif; ?>
<?php if(!empty($totalRanges)): ?>
	<div class="row">
			<div class="subtitle">
				总分分数段成绩分布情况
			</div>
	        <table id="sumscore" style="width: 100%;"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
	            <thead>
					<tr class="bg-info">
		                <th>考试</th>
		                <th>统计人数</th>
		                <?php $__currentLoopData = $totalRanges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		                <th><?php echo e($total['totalRange']['min']); ?>-<?php echo e($total['totalRange']['max']); ?> 分</th>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		            </tr>
	            </thead>
	            <tbody>
	            	<tr>
	            		<td>总分(人)</td>
	            		<td><?php echo e($totalRanges[0]['totalScore']['count']); ?></td>
						<?php $__currentLoopData = $totalRanges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stotal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	            		<td><?php echo e($stotal['totalScore']['number']); ?> </td>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	            	</tr>
	            </tbody>
	        </table>
		</div>
<?php endif; ?>
	<div class="table-pie" style=" width: 100%;height:550px;">
		</div>
