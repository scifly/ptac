<link rel="stylesheet" href="{{ URL::asset('js/bootstrap-daterangepicker/daterangepicker.css') }}">
<div class="box box-default box-solid">
    <div class="box-header with-border">
			@include('partials.list_header')
    </div>
    <div class="box-bod">
        <div class="row" style="margin-top: 10px;">
			<div class="form-horizontal">
				<div class="col-md-6">
					<div class="form-horizontal">
						<!-- 年级 -->
						<div class="form-group">
							@if(isset($grades))
								@include('partials.single_select', [
                                        'id' => 'gradeId',
                                        'label' => '年级:',
                                        'items' => $grades
                                    ])

							@endif
						</div>
						<!-- 班级 -->
						<div class="form-group">
							@if(isset($classes))
								@include('partials.single_select', [
                                        'id' => 'classId',
                                        'label' => '班级:',
                                        'items' => $classes
                                    ])
							@endif
						</div>
					</div>
			   	</div>
			    
			    <div class="col-md-6">
		        	<div class="form-group">
		                <label class="col-sm-3 control-label">时间范围:</label>
						<div class="col-sm-9">
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
        
        
        <table id="data-table" style="width: 100%;"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
            <tr class="bg-info">
                <th>日期</th>
                <th>打卡/异常/未打/合计</th>
                <th>图表</th>
            </tr>
            </thead>
            <tbody>
            	<tr>
            		<td>2017-12-31 - 2018-01-07</td>
            		<td>4/2/1/7</td>
            		<td><div id="main1" style="height: 80px;width: 160px;"></div></td>
            	</tr>
            	<tr>
            		<td>2017-12-31 - 2018-01-07</td>
            		<td>5/3/2/10</td>
            		<td><div id="main2" style="height: 80px;width: 160px;"></div></td>
            	</tr>
            	<tr>
            		<td>2017-12-31 - 2018-01-07</td>
            		<td>2/2/4/8</td>
            		<td><div id="main3" style="height: 80px;width: 160px;"></div></td>
            	</tr>
            	
            </tbody>
        </table>
                
    </div>
</div>
<script src="{{ URL::asset('js/student_attendance_statistics/echarts.simple.min.js') }}"></script>
<script src="{{ URL::asset('js/moment/min/moment.min.js') }}"></script>
<script src="{{ URL::asset('js/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
