<div class="modal fade" id="modal-send">
	<div class="modal-dialog">
		<div class="modal-content modal-lg">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">成绩发送</h4>
			</div>
			<div class="modal-body with-border">
				<div class="form-horizontal">
					@include('shared.single_select', [
						'label' => '考试名称',
						'id' => 'send_exam_id',
						'items' => $exams
					])
					@include('shared.single_select', [
						'label' => '参与班级',
						'id' => 'send_class_id',
						'items' => $classes,
						'icon' => 'fa fa-users'
					])
					<div class="form-group">
						@include('shared.label', ['field' => 'subjects', 'label' => '发布科目'])
						<div class="col-sm-8" id="subject-list" style="margin-top: 6px;">
							{!! Form::checkbox('content', -1, false, [
								'class' => 'minimal'
							]) !!} 总分&nbsp;
							@foreach ($subjects as $key => $value)
								{!! Form::checkbox('content', $key, false, [
									'class' => 'minimal'
								]) !!} {!! $value !!}&nbsp;
							@endforeach
						</div>
					</div>
					@include('shared.multiple_select', [
						'id' => 'items',
						'label' => '发布项目',
						'items' => $items,
						'selectedItems' => $selectedItems ?? null,
						'required' => 'true'
					])
					<div class="form-group" style="margin: 0 5px;">
						<table id="send-table" style="width: 100%; margin-top: 20px;"
							   class="display nowrap table table-striped table-bordered table-hover table-condensed">
							<thead>
							<tr class="bg-info">
								<th style="width: 40px;">
									{!! Form::checkbox('all', null, false, [
										'class' => 'minimal',
										'id' => 'select-all'
									]) !!}
								</th>
								<th>家长姓名</th>
								<th>姓名</th>
								<th>手机号</th>
								<th>内容</th>
							</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a id="preview" href="javascript:" class="btn btn-sm btn-success">预览</a>
				<a id="send-scores" href="#" class="btn btn-sm btn-white" data-dismiss="modal">发送</a>
			</div>
		</div>
	</div>
</div>