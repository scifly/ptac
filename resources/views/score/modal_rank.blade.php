<div class="modal fade" id="modal-rank">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">排名统计</h4>
            </div>
            <div class="modal-body with-border">
                <div class="form-horizontal">
                    <!-- 选择考试 -->
                    @include('shared.single_select', [
                        'id' => 'rank_exam_id',
                        'label' => '选择考试',
                        'items' => $exams
                    ])
                </div>
            </div>
            <div class="modal-footer">
                <a id="rank-scores" href="javascript:" class="btn btn-sm btn-success" data-dismiss="modal">确定</a>
                <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
            </div>
        </div>
    </div>
</div>