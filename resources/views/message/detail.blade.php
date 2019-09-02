<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            {!! Form::button('&times;', [
                'class' => 'close',
                'data-dismiss' => 'modal',
                'aria-hidden' => 'true'
            ]) !!}
            <h4 class="modal-title">{!! $msgTitle; !!}</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="form-horizontal">
                    <div class="form-group">
                        @include('shared.label', ['label' => '通信方式：'])
                        <div class="col-sm-6 msglbl">{!! $commType !!}</div>
                    </div>
                    <div class="form-group">
                        @include('shared.label', ['label' => '发送时间：'])
                        <div class="col-sm-6 msglbl">
                            <span class="badge bg-blue">{!! $sentAt !!}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        @include('shared.label', ['label' => '发送对象：'])
                        <div class="col-sm-6 msglbl">{!! $recipients !!}</div>
                    </div>
                    <div class="form-group">
                        @include('shared.label', ['label' => '发送者：'])
                        <div class="col-sm-6 msglbl">
                            <span class="badge bg-green">{!! $sender !!}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        @include('shared.label', ['label' => '消息内容：'])
                        <div class="col-sm-6 msglbl">{!! $msgBody !!}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            {!! Form::button(
                Html::tag('i', ' 关闭', ['class' => 'fa fa-sign-out']),
                ['class' => 'btn btn-default', 'data-dismiss' => 'modal']
            ) !!}
        </div>
    </div>
</div>