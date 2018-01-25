
<div class="content clearfix">
	<div class="col-lg-12">
		<div class="box box-default box-solid" style="padding: 10px;">
		    <div class="box-header with-border">
					<?php echo $__env->make('partials.list_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
		    </div>
		    <div class="box-bod">
		        <div class="row" style="margin-top: 10px;">
					<div class="form-horizontal">
						<div class="col-md-4">
						    <div class="form-group">
							    <div class="col-sm-12">
									<?php if(isset($grades)): ?>
										<?php echo $__env->make('partials.single_select', [
												'id' => 'gradeId',
												'label' => '年级',
												'items' => $grades
											], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
									<?php endif; ?>
							    </div>
							</div>
					   	</div>
						
						<div class="col-md-4">
						    <div class="form-group">
							    <div class="col-sm-12">
									<?php if(isset($classes)): ?>
										<?php echo $__env->make('partials.single_select', [
												'id' => 'classId',
												'label' => '班级',
												'items' => $classes
											], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
									<?php endif; ?>
							    </div>
							</div>
					   	</div>
					    
					    <div class="col-md-4">
				        	<div class="form-group">
				                <label class="col-sm-3 control-label">时间</label>
								<div class="col-sm-6">
									<div class="input-group">
					                  	<div class="input-group-addon">
					                    	<i class="fa fa-calendar"></i>
					                  	</div>
					                  	<input type="text" class="form-control pull-right" id="reservation">
					                </div>
								</div>
				                
				            </div>
				        </div>
					    
			       	</div>
			        
		        </div>
		
				<div class="row">
					<div class="btns col-md-12" style="width: 120px;float: right;margin-bottom: 10px;">
						<button type="button" class="btn btn-block btn-primary" id="search">查询</button>
					</div>
				</div>
		        <table id="data-table" style="width: 100%;"
		               class="display nowrap table table-striped table-bordered table-hover table-condensed">
		            <thead>
		            <tr class="bg-info">
		                <th>日期</th>
		                <th>正常/异常/未打/合计</th>
		                <th>图表</th>
		            </tr>
		            </thead>
		            <tbody>
						<!--
							
								
									
									
									
								
							
						-->
		
		            </tbody>
		        </table>
		    </div>
		</div>            
	</div>
</div>

<div class="modal fade in" id="student-list" style="padding-right: 17px;">
  	<div class="modal-dialog">
    	<div class="modal-content" style="width: 900px;">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
      			<span aria-hidden="true">×</span></button>
    			<h4 class="modal-title">学生列表</h4>
      		</div>
	      	<div class="modal-body">
	        	<table id="student-table" style="width: 100%;"
	               class="display nowrap table table-striped table-bordered table-hover table-condensed">
		            <thead>
		            <tr class="bg-info">
		                <th>姓名</th>
		                <th>监护人</th>
		                <th>联系方式</th>
		                <th>打卡时间</th>
		                <th>进出状态</th>
		            </tr>
		            </thead>
		            <tbody>
						<!--
							
								
									
									
									
								
							
						-->
		
		            </tbody>
		       	</table>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default pull-left" data-dismiss="modal">关闭</button>
	      	</div>
    	</div>
  	</div>
</div>
