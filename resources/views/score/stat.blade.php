<div class="box box-default box-solid" id="params">
	<div class="box-header with-border">
		@include('shared.form_header')
	</div>
	<div class="box-body">
		<div class="form-horizontal">
			@include('shared.switch', [
				'id' => 'type',
				'label' => '分析类型',
				'options' => ['按考次', '按学生'],
				'value' => null
			])
			@include('shared.single_select', [
				'id' => 'exam_id',
				'label' => '考试',
				'items' => $exams,
				'divId' => 'exam'
			])
			@include('shared.single_select', [
				'id' => 'class_id',
				'label' => '班级',
				'items' => $classes,
				'icon' => 'fa fa-users',
				'divId' => 'class'
			])
			@include('shared.single_select', [
				'id' => 'student_id',
				'label' => '学生',
				'items' => $students,
				'icon' => 'fa fa-child',
				'divId' => 'student'
			])
		</div>
	</div>
	@include('shared.form_buttons', [
		'id' => 'analyze',
		'label' => '分析'
	])
</div>