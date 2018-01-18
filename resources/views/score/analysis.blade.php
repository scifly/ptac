
<section class="content clearfix">
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
        	<div class="tab-content">
				<div class="box box-default box-solid">
					<div class="box-header with-border">
	                    <span id="breadcrumb" style="color: #999; font-size: 13px;">成绩分析</span>
	                </div>
	                @include('score.analysis_data')
					<div class="box-body" id="roles">
						<div class="row">
							<div class="form-horizontal">
								<div class="col-md-12">
								    <div class="form-group">
									    <div class="col-sm-6">
											<label class="col-sm-3 control-label">
												选择学校
											</label>
											<div class="col-sm-6">
												<select class="form-control select2 select2-hidden-accessible" style="width: 100%;" >
													<option>1</option>
													<option>2</option>
													<option>3</option>
													<option>1</option>
												</select>
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
								    	<div class="col-sm-6 ">
											<label class="col-sm-3 control-label">
												分析类型
											</label>
											<div class="col-sm-9 checkbox">
												<label style="padding-left: 0;">
													<input id="byTest" type="radio" name="Rd" class="minimal" value="score" checked=""/>
													按考次
												</label>
												<label>
													<input id="byStudent" type="radio" name="Rd" class="minimal" value="score" />
													按学生
												</label>
											</div>
											
									    </div>
								    </div>
							   	</div>
							</div>
						</div>
						
						<div class="row" style="margin-top: 10px;" id="Test">
							<div class="form-horizontal">
								<div class="col-md-12">
								    <div class="form-group">
									    <div class="col-sm-6">
											<label class="col-sm-3 control-label">
												考试名称
											</label>
											<div class="col-sm-6">
												<select class="form-control select2 select2-hidden-accessible" style="width: 100%;" >
													<option>1</option>
													<option>2</option>
													<option>3</option>
													<option>1</option>
												</select>
											</div>
									    </div>
									    <div class="col-sm-6">
											<label class="col-sm-3 control-label">
												考试范围
											</label>
											<div class="col-sm-6">
												<select class="form-control select2 select2-hidden-accessible" style="width: 100%;" >
													<option>1</option>
													<option>2</option>
													<option>3</option>
													<option>1</option>
												</select>
											</div>
									    </div>
									</div>
							   	</div>
							</div>
						</div>
						
						<div class="row" style="margin-top: 10px;display: none;" id="Student">
							<div class="form-horizontal">
								<div class="col-md-12">
								    <div class="form-group">
									    <div class="col-sm-6">
											<label class="col-sm-3 control-label">
												选择班级
											</label>
											<div class="col-sm-6">
												<select class="form-control select2 select2-hidden-accessible" style="width: 100%;" >
													<option>1</option>
													<option>2</option>
													<option>3</option>
													<option>1</option>
												</select>
											</div>
									    </div>
									    
									    <div class="col-sm-6">
											<label class="col-sm-3 control-label">
												选择学生
											</label>
											<div class="col-sm-6">
												<select class="form-control select2 select2-hidden-accessible" style="width: 100%;" >
													<option>1</option>
													<option>2</option>
													<option>3</option>
													<option>1</option>
												</select>
											</div>
									    </div>
									</div>
							   	</div>
							</div>
						</div>
						
						<div class="row">
					   		<div class="col-md-12" style="text-align: center;">
					   			<button type="button" id="analysis" class="btn btn-primary"><i class="fa fa-fw fa-search" style="margin-right: 5px;"></i>分析</button>
					   		</div>
					   	</div>
						
					</div>
				
				</div>
			</div>
		</div>
	</div>
</div>