<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header', [
            'buttons' => [
                'import' => [
                    'id' => 'import',
                    'label' => '批量导入',
                    'icon' => 'fa fa-arrow-circle-up',
                ],
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
                <th>姓名</th>
                <th>性别</th>
                <th>班级</th>
                <th>学号</th>
                <th>卡号</th>
                <th>住校</th>
                <th>手机</th>
                <th>生日</th>
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

<form class='import' method='post' enctype='multipart/form-data' id="form-import">
    {{csrf_field()}}
    <div class="modal fade" id="import-pupils">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">批量导入</h4>
                </div>
                <div class="modal-body with-border">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {{ Form::label('import', '选择导入文件', [
                                'class' => 'control-label col-sm-3'
                            ]) }}

                            <div class="col-sm-6">
                                <input type="file" id="fileupload" accept=".xls,.xlsx" name="file">
                                <p class="help-block">下载<a href="{{URL::asset('files/student.xlsx')}}">模板</a></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
                    <a id="confirm-import" href="#" class="btn btn-sm btn-success" data-dismiss="modal">确定</a>
                </div>
            </div>
        </div>
    </div>
</form>