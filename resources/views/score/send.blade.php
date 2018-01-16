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
				    	{!! Form::label('examsRange', '考试范围', [
				            'class' => 'col-sm-3 control-label'
				        ]) !!}
					    <div class="col-sm-6">
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
	   	
   </div>
</div>
