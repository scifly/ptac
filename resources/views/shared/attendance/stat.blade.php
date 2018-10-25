<div class="box box-default box-solid" style="padding: 10px;">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="row" style="margin-top: 10px;">
            <div class="form-horizontal">
                @if (isset($grades, $classes))
                    <div class="col-md-3">
                        @include('shared.single_select', [
                            'id' => 'grade_id',
                            'label' => '年级',
                            'items' => $grades,
                            'icon' => 'fa fa-object-group'
                        ])
                    </div>
                    <div class="col-md-3">
                        @include('shared.single_select', [
                            'id' => 'class_id',
                            'label' => '班级',
                            'items' => $classes,
                            'icon' => 'fa fa-users'
                        ])
                    </div>
                @endif
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="col-sm-9">
                            <div class="input-group">
                                <button type="button" class="btn btn-default pull-right" id="daterange">
									<span id="range">
										<i class="fa fa-calendar"></i>&nbsp; 点击选择日期范围
									</span>&nbsp;
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-block btn-default" id="stat">
                        <i class="fa fa-bar-chart"> 统计</i>
                    </button>
                </div>
            </div>
        </div>
        <table id="results" style="width: 100%;"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
            <tr class="bg-info">
                <th class="text-center" style="vertical-align: middle">日期</th>
                <th class="text-center" style="vertical-align: middle">
                    正常 + 异常 + 未打 = 合计
                    <span class="text-gray" style="font-weight: normal;">(点击数字查看明细)</span>
                </th>
                <th class="text-center" style="vertical-align: middle">图表</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    @include('shared.form_overlay')
</div>

<div class="modal fade in" id="detail" style="padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 900px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">考勤明细</h4>
            </div>
            <div class="modal-body">
                <table id="records" style="width: 100%;"
                       class="display nowrap table table-striped table-bordered table-hover table-condensed">
                    <thead>
                        @foreach($titles as $title)
                            <th class="text-center">{!! $title !!}</th>
                        @endforeach
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