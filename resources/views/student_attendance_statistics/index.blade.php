<link rel="stylesheet" href="{{ URL::asset('js/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ URL::asset('js/plugins/select2/css/select2.min.css') }}">
<style>
	.select2-container .select2-selection--single{
		height: 34px;
	}
</style>
<div class="box box-default box-solid">
    <div class="box-header with-border">
    	
    </div>
    <div class="box-bod">
        <div class="row" style="margin-top: 10px;">
			<div class="form-horizontal">
				
				<div class="col-md-4">
				    <div class="form-group">
					    <label class="col-sm-3 control-label">
					        	年级
					    </label>
					    <div class="col-sm-9">
					        <div class="input-group">
					            <div class="input-group-addon">
					                <i class="fa fa-users"></i>
					            </div>
					            <select name="grades" class='form-control select2 select2-hidden-accessible' style="width: 100%;">
					                
					               	<option value="1">一年级</option>
					                <option value="2">2</option>
					                <option value="3">3</option>
					                <option value="4">4</option>    
					            </select>
					        </div>
					    </div>
					</div>
			   	</div>
				
				<div class="col-md-4">
				    <div class="form-group">
					    <label class="col-sm-3 control-label">
					        	班级
					    </label>
					    <div class="col-sm-9">
					        <div class="input-group">
					            <div class="input-group-addon">
					                <i class="fa fa-users"></i>
					            </div>
					            <select name="classes" class='form-control select2' style="width: 100%;">
					                
					               	<option value="1">1</option>
					                <option value="2">2</option>
					                <option value="3">3</option>
					                <option value="4">4</option>    
					            </select>
					        </div>
					    </div>
					</div>
			   	</div>
			    
			    <div class="col-md-4">
		        	<div class="form-group">
		                <label class="col-sm-3 control-label">时间</label>
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
        
        <div class="row">
        	<div class="btns col-md-12" style="width: 120px;float: right;margin-bottom: 10px;">
        		<button type="button" class="btn btn-block btn-primary">查询</button>
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
<script src="{{ URL::asset('js/plugins/select2/js/select2.full.min.js') }}"></script>