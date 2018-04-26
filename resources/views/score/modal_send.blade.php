<div class="modal fade" id="modal-send">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">成绩发送</h4>
			</div>
			<div class="modal-body with-border">
				<div class="form-horizontal">
					@include('partials.single_select', [
						'label' => '考试名称',
						'id' => 'send_exam_id',
						'items' => $exams
					])
					@include('partials.single_select', [
						'label' => '考试范围',
						'id' => 'send_class_id',
						'items' => $classes
					])
					<div class="form-group" id="subject-list">
						{{ Form::label('subjects', '发布内容', [
							'class' => 'control-label col-sm-3'
						]) }}
						<div class="col-sm-6">
							{{ Form::checkbox('subjects', -1, false, [
								'class' => 'minimal'
							]) }} 总分
							@foreach ($subjects as $s)
								{{ Form::checkbox('content', $s['id'], false, [
									'class' => 'minimal'
								]) }} {{ $s['name'] }}
							@endforeach
						</div>
					</div>
					<div class="form-group" id="item-list">
						{{ Form::label('items', '发布项目', [
							'class' => 'control-label col-sm-3'
						]) }}
						<div class="col-sm-6">
							{{ Form::checkbox('items', 'score', false, ['class' => 'minimal']) }} 分数
							{{ Form::checkbox('items', 'grade_rank', false, ['class' => 'minimal']) }} 年排名
							{{ Form::checkbox('items', 'class_rank', false, ['class' => 'minimal']) }} 班排名
							{{ Form::checkbox('items', 'grade_average', false, ['class' => 'minimal']) }} 年平均
							{{ Form::checkbox('items', 'class_average', false, ['class' => 'minimal']) }} 班平均
							{{ Form::checkbox('items', 'grade_max', false, ['class' => 'minimal']) }} 年最高
							{{ Form::checkbox('items', 'class_max', false, ['class' => 'minimal']) }} 班最高
							{{ Form::checkbox('items', 'grade_min', false, ['class' => 'minimal']) }} 年最低
							{{ Form::checkbox('items', 'class_min', false, ['class' => 'minimal']) }} 班最低
						</div>
					</div>
					<div class="form-group">
						<table id="send-table" style="width: 100%; margin-top: 20px;"
							   class="display nowrap table table-striped table-bordered table-hover table-condensed">
							<thead>
							<tr class="bg-info">
								<th width="40">
									{{ Form::checkbox('all', null, false, [
										'class' => 'minimal',
										'id' => 'select-all'
									]) }}
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
				<a id="preview" href="javascript:" class="btn btn-sm btn-success" data-dismiss="modal">预览</a>
				<a id="send-scores" href="#" class="btn btn-sm btn-white" data-dismiss="modal">发送</a>
			</div>
		</div>
	</div>
</div>
{{--<div class="box box-default box-solid" id="send_main" style="display: none;">--}}
	{{--<div class="box-body">--}}
		{{--<div class="overlay">--}}
		    {{--<i class="fa fa-refresh fa-spin"></i>--}}
		{{--</div>--}}
		{{--<div class="row">--}}
	        {{--<div class="box-tools pull-right">--}}
	            {{--<i class="fa fa-close " id="close-send"></i>--}}
	        {{--</div>--}}
		{{--</div>--}}
		{{--<div class="row" style="margin-top: 20px;">--}}
			{{--<div class="form-horizontal">--}}
				{{--<div class="col-md-6">--}}
				    {{--<div class="form-group">--}}
					    {{--<div class="col-sm-12">--}}
							{{--@include('partials.single_select', [--}}
								{{--'label' => '考试名称',--}}
								{{--'id' => 'exam_id',--}}
								{{--'items' => $examScore--}}
							{{--])--}}
					    {{--</div>--}}
					{{--</div>--}}
			   	{{--</div>--}}
			   	{{--<div class="col-md-6">--}}
				    {{--<div class="form-group">--}}
					    {{--<div class="col-sm-12">--}}
							{{--@include('partials.single_select', [--}}
								{{--'label' => '考试范围',--}}
								{{--'id' => 'squad_id',--}}
								{{--'items' => $classes--}}
							{{--])--}}
					    {{--</div>--}}
					{{--</div>--}}
			   	{{--</div>--}}
			{{--</div>--}}
	   {{--</div>--}}
	   {{--<div class="row">--}}
	   		{{--<div class="form-horizontal">--}}
	   			{{--<div class="col-md-12">--}}
	   				{{--<div class="form-group">--}}
				    	{{--<label class="col-sm-2 control-label">发布内容</label>--}}
				    	{{--<div class="col-sm-10">--}}
				    		{{--<div class="checkbox" id="subject-list">--}}
					    		{{--<label>--}}
									{{--<input type="checkbox" class="minimal" value="-1">--}}
									{{--总分--}}
								{{--</label>--}}
								{{--@if (isset($subjects))--}}
									{{--@foreach($subjects as $s)--}}
										{{--@if($s)--}}
										{{--<label>--}}
											{{--<input type="checkbox" class="minimal" value="{{$s['id']}}">--}}
											{{--{{$s['name']}}--}}
										{{--</label>--}}
										{{--@endif--}}
									{{--@endforeach--}}
								{{--@endif--}}
				   			{{--</div>--}}
				    	{{--</div>--}}
				    {{--</div>--}}
			   	{{--</div>--}}
	   		{{--</div>--}}
	   	{{--</div>--}}

	   	{{--<div class="row">--}}
	   		{{--<div class="form-horizontal">--}}
	   			{{--<div class="col-md-12">--}}
		   			{{--<div class="form-group">--}}
				    	{{--<label class="col-sm-2 control-label">发送项目</label>--}}
				    	{{--<div class="col-sm-10">--}}
				    		{{--<div class="checkbox" id="project-list">--}}
					    		{{--<label>--}}
					   				{{--<input type="checkbox" class="minimal" value="score">--}}
					   				{{--分数--}}
					   			{{--</label>--}}
					   			{{--<label>--}}
					   				{{--<input type="checkbox" class="minimal" value="grade_rank">--}}
					   				{{--年排名--}}
					   			{{--</label>--}}
					   			{{--<label>--}}
					   				{{--<input type="checkbox" class="minimal" value="class_rank">--}}
					   				{{--班排名--}}
					   			{{--</label>--}}
					   			{{--<label>--}}
					   				{{--<input type="checkbox" class="minimal" value="grade_average">--}}
					   				{{--年平均--}}
					   			{{--</label>--}}
					   			{{--<label>--}}
					   				{{--<input type="checkbox" class="minimal" value="class_average">--}}
					   				{{--班平均--}}
					   			{{--</label>--}}
					   			{{--<label>--}}
					   				{{--<input type="checkbox" class="minimal" value="grade_max">--}}
					   				{{--年最高--}}
					   			{{--</label>--}}
					   			{{--<label>--}}
					   				{{--<input type="checkbox" class="minimal" value="class_max">--}}
					   				{{--班最高--}}
					   			{{--</label>--}}
					   			{{--<label>--}}
					   				{{--<input type="checkbox" class="minimal" value="grade_min">--}}
					   				{{--年最低--}}
					   			{{--</label>--}}
					   			{{--<label>--}}
					   				{{--<input type="checkbox" class="minimal" value="class_min">--}}
					   				{{--班最低--}}
					   			{{--</label>--}}

				   			{{--</div>--}}
				    	{{--</div>--}}
				    {{--</div>--}}
	   			{{--</div>--}}
	   		{{--</div>--}}
	   	{{--</div>--}}
	   	{{--<div class="row">--}}
	   		{{--<div class="col-md-12" style="text-align: center;">--}}
	   			{{--<button type="button" id="btn-browse" class="btn btn-primary" style="margin-right: 30px;">预览</button>--}}
	   			{{--<button type="button" id="btn-send-message" class="btn btn-success">发送</button>--}}
	   		{{--</div>--}}

	   	{{--</div>--}}
		{{--<table id="send-table" style="width: 100%;margin-top: 20px;"--}}
			   {{--class="display nowrap table table-striped table-bordered table-hover table-condensed">--}}
			{{--<thead>--}}
			{{--<tr class="bg-info">--}}
				{{--<th width="40">--}}
					{{--<label>--}}
						{{--<input type="checkbox" class="minimal" id="table-checkAll">--}}
					{{--</label>--}}
				{{--</th>--}}
				{{--<th>家长姓名</th>--}}
				{{--<th>姓名</th>--}}
				{{--<th>手机号</th>--}}
				{{--<th>内容</th>--}}
			{{--</tr>--}}
			{{--</thead>--}}
			{{--<tbody></tbody>--}}
		{{--</table>--}}

   {{--</div>--}}
{{--</div>--}}
