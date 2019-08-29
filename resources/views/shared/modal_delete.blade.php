<!-- #modal-dialog -->
<div class="modal fade" id="modal-delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                {!! Form::button('×', [
                    'class' => 'close',
                    'data-dismiss' => 'modal',
                    'aria-hidden' => 'true'
                ]) !!}
                <h4 class="modal-title">删除记录</h4>
            </div>
            <div class="modal-body">
                删除后无法恢复。点击‘确定’继续，‘取消’返回。
            </div>
            <div class="modal-footer">
                {!! Html::link('#', '取消', [
                    'class' => 'btn btn-sm btn-white',
                    'data-dismiss' => 'modal'
                ]) !!}
                {!! Html::link('#', '确定', [
                    'id' => 'confirm-delete',
                    'class' => 'btn btn-sm btn-success',
                    'data-dismiss' => 'modal'
                ]) !!}
            </div>
        </div>
    </div>
</div>