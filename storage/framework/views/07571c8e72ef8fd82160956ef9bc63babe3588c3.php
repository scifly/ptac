	<div class="row">
		<div class="title">
			<?php if(count($student) != 0): ?>
			<?php echo e($student->user->realname); ?>学生成绩统计
			<?php else: ?>
				学生成绩统计
			<?php endif; ?>
		</div>
        <div class="box-tools pull-right">
            <i class="fa fa-close " id="close-data"></i>
        </div>
		
	</div>
	
	<div class="row">
		<div class="subtitle">
			<?php if(count($student) != 0): ?>
				<?php echo e($student->user->realname); ?>同学考试情况
			<?php else: ?>
				考试情况
			<?php endif; ?>
		</div>
		<input class="number" id="sub_number" value="<?php echo e(count($subjectName)); ?>" hidden>
        <table id="scores" style="width: 100%;"
           class="display nowrap table table-striped table-bordered table-hover table-condensed">
			<thead>
				<tr class="bg-info">
	                <th>序号</th>
	                <th>考试名称</th>
	                <th>考试时间</th>
					<?php if(!empty($subjectName)): ?>
	                <?php $__currentLoopData = $subjectName; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<th class="subjectName"> <?php echo e($name); ?> </th>
	                <th>班排</th>
	                <th>年排</th>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	                <th class="subjectName">总分</th>
	                <th>班排</th>
	                <th>年排</th>
					<?php endif; ?>
				</tr>
            </thead>
            <tbody>
			<?php if(!empty($examScore)): ?>
				<?php $__currentLoopData = $examScore; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td><?php echo e($exam['examId']); ?></td>
					<td class="testName"><?php echo e($exam['examName']); ?></td>
					<td><?php echo e($exam['examTime']); ?></td>
					<?php $__currentLoopData = $exam['score']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<td><?php echo e($item['score']); ?></td>
					<td class="classrankeItem"><?php echo e($item['class_rank']); ?></td>
					<td class="graderankeItem"><?php echo e($item['grade_rank']); ?></td>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<td><?php echo e($exam['scoreTotal']['score']); ?></td>
					<td class="classrankeItem"><?php echo e($exam['scoreTotal']['class_rank']); ?></td>
					<td class="graderankeItem"><?php echo e($exam['scoreTotal']['grade_rank']); ?></td>
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php endif; ?>
            </tbody>
        </table>
	</div>
	
	<div class="row">
		<div class="subtitle">
			<?php if(count($student) != 0): ?>
				<?php echo e($student->user->realname); ?>各科班级排名变化
			<?php else: ?>
				各科班级排名变化
			<?php endif; ?>
		</div>
		<div id="classranke">
			
		</div>
	</div>
	
	<div class="row">
		<div class="subtitle">
			<?php if(count($student) != 0): ?>
				<?php echo e($student->user->realname); ?>各科班级排名变化
			<?php else: ?>
				各科年级排名变化
			<?php endif; ?>
		</div>
		<div id="graderanke">
			
		</div>
	</div>