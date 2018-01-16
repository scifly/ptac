<div class="box box-default box-solid" id="send_main" style="display: block;">
	<div class="box-body">
		
		<div class="row" >
			<div class="form-horizontal">
				<div class="col-md-6">
				    <div class="form-group">
				    	{!! Form::label('examsName', '考试名称', [
				            'class' => 'col-sm-3 control-label'
				        ]) !!}
					    <div class="col-sm-6">
							<select class="form-control select2 select2-hidden-accessible" style="width: 100%;" id="examsName" name="examsName" tabindex="-1" aria-hidden="true">
								<option value="1">考试1</option>
								<option value="2">考试2</option>
							</select>
					    </div>
					</div>
			   	</div>
			   	
			   	<div class="col-md-6">
				    <div class="form-group">
				    	{!! Form::label('examsRange', '考试范围', [
				            'class' => 'col-sm-3 control-label'
				        ]) !!}
					    <div class="col-sm-6">
							<select class="form-control select2 select2-hidden-accessible" style="width: 100%;" id="examsRange" name="examsRange" tabindex="-1" aria-hidden="true">
								
								
							</select>
					    </div>
					</div>
			   	</div>
			</div>
	   	</div>
	   	
   </div>
</div>
