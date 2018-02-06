@include('score.send')
<div class="box box-default box-solid" id="score">
    <div class="box-header with-border">
        @include('partials.list_header', [
            'buttons' => [
                'send' => [
                    'id' => 'send',
                    'label' => '成绩发送',
                    'icon' => 'fa fa-send-o',
                ],
                'import' => [
                    'id' => 'import',
                    'label' => '批量导入',
                    'icon' => 'fa fa-arrow-circle-up',
                ],
                 'statistics' => [
                    'id' => 'statistics',
                    'label' => ' 排名统计',
                    'icon' => 'fa fa-bar-chart-o'
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
                <th>年级</th>
                <th>班级</th>
                <th>学号</th>
                <th>科目名称</th>
                <th>考试名称</th>
                <th>班级排名</th>
                <th>年级排名</th>
                <th>成绩</th>
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

<!-- 导入excel -->
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
                        <!-- 选择考试 -->
                        <div class="form-group">
                            @if(isset($examarr))
                                @include('partials.single_select', [
                                        'id' => 'exam',
                                        'label' => '选择考试',
                                        'items' => $examarr
                                    ])
                            @endif
                        </div>
                        <!-- 班级 -->
                        <div class="form-group">
                            @if(isset($classes))
                                @include('partials.single_select', [
                                        'id' => 'classId',
                                        'label' => '班级',
                                        'items' => $classes
                                    ])
                            @endif
                        </div>
                        <div class="form-group">
                            {{ Form::label('import', '选择导入文件', [
                                'class' => 'control-label col-sm-3'
                            ]) }}

                            <div class="col-sm-6">
                                <input type="file" id="fileupload" accept=".xls,.xlsx" name="file">
                                <p class="help-block">下载<a href="javascript:void(0)">模板</a></p>
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

<!-- 成绩统计 -->
<div class="modal fade" id="statistics-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">成绩统计</h4>
            </div>
            <div class="modal-body with-border">
                <div class="form-horizontal">
                    <!-- 选择考试 -->
                    <div class="form-group">
                        @if(isset($examarr))
                            @include('partials.single_select', [
                                    'id' => 'exam-sta',
                                    'label' => '选择考试',
                                    'items' => $examarr
                                ])
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
                <a id="confirm-statistics" href="javascript:" class="btn btn-sm btn-success" data-dismiss="modal">确定</a>
            </div>
        </div>
    </div>
</div>