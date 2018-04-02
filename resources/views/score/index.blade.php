@include('partials.index')
@include('score.send')

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