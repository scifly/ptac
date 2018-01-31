<section class="content clearfix">
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
        	<div class="tab-content">
				<div class="box box-default box-solid">
					<div class="box-header with-border">
	                    <span id="breadcrumb" style="color: #999; font-size: 13px;">成绩分析</span>
	                </div>
                    <div class="box-body" id="datas" style="display: none;">

                    </div>
                    {{--@include('score.analysis_student_data')--}}
                    
					<div class="box-body" id="roles" style="display: block;">
						<div class="row">
							<div class="form-horizontal">
								<div class="col-md-12">
								    <div class="form-group">
								    	<div class="col-sm-6 ">
											<label class="col-sm-3 control-label">
												分析类型
											</label>
											<div class="col-sm-9 checkbox" id="analysis-type">
												<label style="padding-left: 0;">
													<input id="byTest" type="radio" name="exam_type" class="minimal" value="0" checked=""/>
													按考次
												</label>
												{{--<label>--}}
													{{--<input id="byStudent" type="radio" name="exam_type" class="minimal" value="1" />--}}
													{{--按学生--}}
												{{--</label>--}}
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
												<select id="exam_id" class="form-control select2 select2-hidden-accessible" style="width: 100%;" >
                                                    @foreach($examarr as $key => $exam)
                                                    <option value="{{ $key }}">{{ $exam }}</option>
                                                    @endforeach
												</select>
											</div>
									    </div>
									    <div class="col-sm-6">
											<label class="col-sm-3 control-label">
												选择班级
											</label>
											<div class="col-sm-6">
												<select id="squad" class="form-control select2 select2-hidden-accessible" style="width: 100%;" >
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
												<select id="class_id" class="form-control select2 select2-hidden-accessible" style="width: 100%;" >
													@foreach($classes as $key => $class)
                                                    <option value="{{ $key }}">{{ $class }}</option>
                                                    @endforeach
												</select>
											</div>
									    </div>
									    
									    <div class="col-sm-6">
											<label class="col-sm-3 control-label">
												选择学生
											</label>
											<div class="col-sm-6">
												<select id="student_id" class="form-control select2 select2-hidden-accessible" style="width: 100%;" >
													@foreach($students as $key => $student)
                                                    <option value="{{ $key }}">{{ $student }}</option>
                                                        @endforeach
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
</section>