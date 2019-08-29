<form class='import' method='post' enctype='multipart/form-data' id="form-import">
{!! Form::open(['']) !!}
    {!! csrf_field() !!}
    <div class="modal fade" id="upload">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">批量导入</h4>
                </div>
                <div class="modal-body with-border">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('import', '选择导入文件', [
                                'class' => 'control-label col-sm-3'
                            ]) !!}
                            <div class="col-sm-6">
                                {!! Form::file('file', [
                                    'id' => 'fileupload',
                                    'accept' => '.xls,.xlsx'
                                ]) !!}
                                <p class="help-block">
                                    下载<a href="{!! URL::asset($importTemplate) !!}">模板</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {!! Html::link('#', '确定', [
                        'id' => 'confirm-import',
                        'class' => 'btn btn-sm btn-success',
                        'data-dismiss' => 'modal'
                    ]) !!}
                    {!! Html::link('#', '取消', [
                        'class' => 'btn btn-sm btn-white',
                        'data-dismiss' => 'modal'
                    ]) !!}
                </div>
            </div>
        </div>
    </div>
</form>