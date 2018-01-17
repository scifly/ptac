<div class="box box-default box-solid" id="send_main" style="display: none;">
	<div class="box-body">
		<div class="row">
	        <div class="box-tools pull-right">
	            <i class="fa fa-close " id="close-send" style="cursor: pointer;font-size: 22px;margin-right: 20px;"></i>
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
									<input type="checkbox" class="minimal">
									总分
								</label>
								@foreach($subjects as $s)
									<label>
										<input type="checkbox" class="minimal">
										{{$s['name']}}
									</label>
								@endforeach
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
				    		<div class="checkbox">
					    		<label>
					   				<input type="checkbox" class="minimal">
					   				分数
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal">
					   				年排名
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal">
					   				班排名
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal">
					   				年平均
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal">
					   				班平均
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal">
					   				年最高
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal">
					   				班最高
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal">
					   				年最低
					   			</label>
					   			<label>
					   				<input type="checkbox" class="minimal">
					   				班最低
					   			</label>
					   			
				   			</div>
				    	</div>
				    </div>
	   			</div>
	   		</div>
	   	</div>
	   	
	   	<div class="row">
	   		<div class="col-md-6" style="text-align: right;">
	   			<button type="button" id="btn-browse" class="btn btn-primary">浏览</button>
	   		</div>
	   		<div class="col-md-6">
	   			<button type="button" id="btn-send-message" class="btn btn-success">发送</button>
	   		</div>
	   	</div>
	   	
        <table id="send-table" style="width: 100%;margin-top: 20px;"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
				<tr class="bg-info">
	                <th width="40">#</th>
	                <th width="120">家长姓名</th>
	                <th width="120">姓名</th>
	                <th width="300">手机号</th>
	                <th>内容</th>
	            </tr>
            </thead>
            <tbody></tbody>
        </table>
	    
   </div>
</div>
