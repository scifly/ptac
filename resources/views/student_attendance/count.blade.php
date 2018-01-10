<link rel="stylesheet" href="{{ URL::asset('js/bootstrap-daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ URL::asset('js/plugins/select2/css/select2.min.css') }}">
<style>
	.select2-container .select2-selection--single{
		height: 34px;
	}
	.attendances-date,.attendances-data{
		font-size: 18px;
		line-height: 100%;
		height: 100%;
	}
	.attendances-data .normal,.attendances-data .abnormal,.attendances-data .surplus{
		cursor: pointer;
	}
	.normal{
		color: #83db74;
	}
	.abnormal{
		color:#fdde52 ;
	}
	.surplus{
		color: #fc7f4e;
	}
</style>
<div class="content clearfix">
	<div class="col-lg-12">
		<div class="box box-default box-solid" style="padding: 10px;">
		    <div class="box-header with-border">
					@include('partials.list_header')
		    </div>
		    <div class="box-bod">
		        <div class="row" style="margin-top: 10px;">
					<div class="form-horizontal">
						<div class="col-md-4">
						    <div class="form-group">
							    <div class="col-sm-12">
									@if(isset($grades))
										@include('partials.single_select', [
												'id' => 'gradeId',
												'label' => '年级',
												'items' => $grades
											])
									@endif
							    </div>
							</div>
					   	</div>
						
						<div class="col-md-4">
						    <div class="form-group">
							    <div class="col-sm-12">
									@if(isset($classes))
										@include('partials.single_select', [
												'id' => 'classId',
												'label' => '班级',
												'items' => $classes
											])
									@endif
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
						<!--{{--@if($item)--}}
							{{--@foreach($item as $t =>$i)--}}
								{{--<tr>--}}
									{{--<td>{{$i['date']}}</td>--}}
									{{--<td>{{$i['normal']}}/{{$i['abnormal']}}/{{$i['surplus']}}/{{$i['all']}}</td>--}}
									{{--<td><div id="main{{$t}}" style="height: 80px;width: 160px;"></div></td>--}}
								{{--</tr>--}}
							{{--@endforeach--}}
						{{--@endif--}}-->
		
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
						<!--{{--@if($item)--}}
							{{--@foreach($item as $t =>$i)--}}
								{{--<tr>--}}
									{{--<td>{{$i['date']}}</td>--}}
									{{--<td>{{$i['normal']}}/{{$i['abnormal']}}/{{$i['surplus']}}/{{$i['all']}}</td>--}}
									{{--<td><div id="main{{$t}}" style="height: 80px;width: 160px;"></div></td>--}}
								{{--</tr>--}}
							{{--@endforeach--}}
						{{--@endif--}}-->
		
		            </tbody>
		       	</table>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default pull-left" data-dismiss="modal">关闭</button>
	      	</div>
    	</div>
  	</div>
</div>

<script type="text/javascript" src="{{ URL::asset('js/student_attendance/echarts.simple.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/moment/min/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
