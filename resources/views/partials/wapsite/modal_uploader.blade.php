<div class="modal fade" id="modalPic">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                {!! Form::button('×', [
                    'class' => 'close',
                    'data-dismiss' => 'modal',
                    'aria-hidden' => 'true'
                ]) !!}
                <h4 class="modal-title" id="myModalLabel">
                    上传文件
                </h4>
            </div>
            <div class="modal-body">
                {!! Form::file('images[]', [
                    'id' => 'uploadFiles',
                    'accept' => 'image/*',
                    'multiple'
                ]) !!}
            </div>
            <div class="modal-footer">
                {!! Form::button('关闭', [
                    'class' => 'btn btn-default',
                    'data-dismiss' => 'modal'
                ]) !!}
            </div>
        </div>
    </div>
</div>