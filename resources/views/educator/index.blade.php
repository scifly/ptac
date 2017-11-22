<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header', [
            'buttons' => [
                'export' => [
                    'id' => 'export',
                    'label' => '批量导出',
                    'icon' => 'fa fa-arrow-circle-down'
                ]
            ]
        ])
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
			<tr class="bg-info">
                <th>#</th>
                <th>教职工名称</th>
                <th>所属学校</th>
                <th>可用短信条数</th>
                <th>创建于</th>
                <th>更新于</th>
                <th>状态</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    @include('partials.form_overlay')
</div>


<!-- 导出excel -->
<div class="modal fade" id="export-pupils">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">导出</h4>
            </div>
            <div class="modal-body with-border">
                <div class="form-horizontal">
                    <!-- 所属学校 -->
                    <div class="form-group">
                        @if(isset($schools))
                            @include('partials.single_select', [
                                    'id' => 'schoolId',
                                    'label' => '所属学校',
                                    'items' => $schools
                                ])
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
                <a id="confirm-bind" href="javascript:" class="btn btn-sm btn-success">确定</a>
            </div>
        </div>
    </div>
</div>
