<div class="modal fade" id="modal-export">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class='import' method='post' enctype='multipart/form-data' id="form-import">
                {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">批量导出</h4>
                </div>
                <div class="modal-body with-border">
                    <div class="form-horizontal">
                        <!-- 选择考试 -->
                        @include('shared.single_select', [
                            'id' => 'export_exam_id',
                            'label' => '考试',
                            'items' => $exams
                        ])
                        <!-- 班级 -->
                        @include('shared.single_select', [
                            'id' => 'export_class_id',
                            'label' => '班级',
                            'items' => $classes
                        ])
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="export-scores" href="#" class="btn btn-sm btn-success" data-dismiss="modal">确定</a>
                    <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
                </div>
            </form>
        </div>
    </div>
</div>