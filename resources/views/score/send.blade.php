<div class="box box-default box-solid" id="send_main" style="display: none;">
	<div class="box-body">
		<div class="overlay">
		    <i class="fa fa-refresh fa-spin"></i>
		</div>
		<div class="row">
	        <div class="box-tools pull-right">
	            <i class="fa fa-close " id="close-send"></i>
	        </div>
		</div>

		<div class="row" style="margin-top: 20px;">
			<div class="form-horizontal">
				<div class="col-md-6">
				    <div class="form-group">

					    <div class="col-sm-12">
							@include('partials.single_select', [
								'label' => '考试名称',
								'id' => 'exam_id',
								'items' => $exams
							])
					    </div>
					</div>
			   	</div>

			   	<div class="col-md-6">
				    <div class="form-group">
					    <div class="col-sm-12">
							@include('partials.single_select', [
								'label' => '考试范围',
								'id' => 'squad_id',
								'items' => $classes
							])
					    </div>
					</div>
			   	</div>
			</div>
	   </div>
	   <div class="row">
	   		<div class="form-horizontal">
	   			<div class="col-md-12">
	   				<div class="form-group">
				    	<label class="col-sm-2 control-label">发布内容</label>
				    	<div class="col-sm-10">
				    		<div class="checkbox" id="subject-list">
					    		<label>
									<input type="checkbox" class="minimal" value="-1">
									总分
								</label>
								@if(($subjects))
									@foreach($subjects as $s)
										@if($s)
										<label>
											<input type="checkbox" class="minimal" value="{{$s['id']}}">
											{{$s['name']}}
										</label>
										@endif
									@endforeach
								@endif
				   			</div>
				    	</div>
				    </div>
			   	</div>
	   		</div>
	   	</div>

	   	<div class="row">
	   		<div class="form-horizontal">
	   			<div class="col-md-12">
		   			<div class="form-group">
				    	<label class="col-sm-2 control-label">发送项目</label>
				    	<div class="col-sm-10">
				    		<div class="checkbox" id="project-list">
					    		<label>
					   				<input type="checkbox" class="minimal" value="score">
					   				分数
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal" value="grade_rank">
					   				年排名
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal" value="class_rank">
					   				班排名
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal" value="grade_average">
					   				年平均
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal" value="class_average">
					   				班平均
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal" value="grade_max">
					   				年最高
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal" value="class_max">
					   				班最高
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal" value="grade_min">
					   				年最低
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal" value="class_min">
					   				班最低
					   			</label>

				   			</div>
				    	</div>
				    </div>
	   			</div>
	   		</div>
	   	</div>

	   	<div class="row">
	   		<div class="col-md-12" style="text-align: center;">
	   			<button type="button" id="btn-browse" class="btn btn-primary" style="margin-right: 30px;">浏览</button>
	   			<button type="button" id="btn-send-message" class="btn btn-success">发送</button>
	   		</div>

	   	</div>

        <table id="send-table" style="width: 100%;margin-top: 20px;"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
				<tr class="bg-info">
	                <th width="40">
	                	<label>
							<input type="checkbox" class="minimal" id="table-checkAll">
						</label>
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
