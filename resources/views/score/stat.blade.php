<div class="box box-default box-solid">
	<div class="box-header with-border">
		@include('partials.form_header')
	</div>
	<div class="box-body">
		<div class="form-horizontal">
			@include('partials.enabled', [
				'id' => 'type',
				'label' => '分析类型',
				'options' => ['按考次', '按学生'],
				'value' => null
			])
			@include('partials.single_select', [
				'id' => 'exam_id',
				'label' => '考试',
				'items' => $exams,
				'divId' => 'exam'
			])
			@include('partials.single_select', [
				'id' => 'class_id',
				'label' => '班级',
				'items' => $classes,
				'icon' => 'fa fa-users',
				'divId' => 'class'
			])
			@include('partials.single_select', [
				'id' => 'student_id',
				'label' => '学生',
				'items' => $students,
				'icon' => 'fa fa-child',
				'divId' => 'student'
			])
			<div class="form-group">
				{!! Form::label('result', ['class' => 'col-sm-3 control-label']) !!}
				<div id="result" class="col-sm-6">
					
				</div>
			</div>
		</div>
	</div>
	@include('partials.form_buttons', [
		'id' => 'analyze',
		'label' => '分析'
	])
</div>