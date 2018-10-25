<div class="modal fade" id="modal-import">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class='import' method='post' enctype='multipart/form-data' id="form-import">
                {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">批量导入</h4>
                </div>
                <div class="modal-body with-border">
                    <div class="form-horizontal">
                        <!-- 选择考试 -->
                        @include('shared.single_select', [
                            'id' => 'import_exam_id',
                            'label' => '选择考试',
                            'items' => $exams
                        ])
                        <!-- 班级 -->
                        @include('shared.single_select', [
                            'id' => 'import_class_id',
                            'label' => '班级',
                            'items' => $classes
                        ])
                        <div class="form-group">
                            {{ Form::label('import', '选择导入文件', [
                                'class' => 'control-label col-sm-3'
                            ]) }}
                            <div class="col-sm-6">
                                <input type="file" id="fileupload" accept=".xls,.xlsx" name="file">
                                <p class="help-block">下载<a href="{{URL::asset($importTemplate)}}">模板</a></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="import-scores" href="#" class="btn btn-sm btn-success" data-dismiss="modal">确定</a>
                    <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
                </div>
            </form>
        </div>
    </div>
</div>