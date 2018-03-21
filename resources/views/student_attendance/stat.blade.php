<div class="box box-default box-solid" style="padding: 10px;">
	<div class="box-header with-border">
		@include('partials.form_header')
	</div>
	<div class="box-body">
		<div class="row" style="margin-top: 10px;">
			<div class="form-horizontal">
				<div class="col-md-3">
					@include('partials.single_select', [
						'id' => 'grade_id',
						'label' => '年级',
						'items' => $grades,
						'icon' => 'fa fa-object-group'
					])
				</div>
				<div class="col-md-3">
					@include('partials.single_select', [
						'id' => 'class_id',
						'label' => '班级',
						'items' => $classes,
						'icon' => 'fa fa-users'
					])
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="col-sm-3 control-label">时间范围</label>
						<div class="col-sm-9">
							<div class="input-group">
								@include('partials.icon_addon', ['class' => 'fa-calendar'])
								<input type="text" class="form-control pull-right" id="reservation" title="时间范围">
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-2">
					<button type="button" class="btn btn-block btn-default" id="search">
						<i class="fa fa-bar-chart"> 统计</i>
					</button>
				</div>
			</div>
		</div>
		<table id="data-table" style="width: 100%;"
			   class="display nowrap table table-striped table-bordered table-hover table-condensed">
			<thead>
			<tr class="bg-info">
				<th>日期</th>
				<th>
					正常 + 异常 + 未打 = 合计
					<span class="text-gray" style="font-weight: normal;">(点击数字查看明细)</span>
				</th>
				<th>图表</th>
			</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	@include('partials.form_overlay')
</div>

<div class="modal fade in" id="student-list" style="padding-right: 17px;">
  	<div class="modal-dialog">
    	<div class="modal-content" style="width: 900px;">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
      			<span aria-hidden="true">×</span></button>
    			<h4 class="modal-title">考勤明细</h4>
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
		            </tbody>
		       	</table>
	      	</div>
	      	<div class="modal-footer">
				<a id="export" href="#" class="btn btn-sm btn-success" data-dismiss="modal">导出</a>
				<a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
	      	</div>
    	</div>
  	</div>
</div>