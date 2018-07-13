<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                &times;
            </button>
            <h4 class="modal-title" id="detail-title">
                {!! $msgTitle; !!}
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-xs-12" id="detail-body">
                    <div>{!! $msgBody !!}</div>
                    <div>
                        @if ($app)
                            <img style="height: 16px; vertical-align: sub;" alt="" src="{!! $app['square_logo_url'] !!}"/>
                            {!! $app['name'] !!}
                        @else
                            未知
                        @endif
                    </div>
                    <div>
                        <span class="control-label">发送时间:</span> {!! $sentAt !!}
                    </div>
                    <div>
                        <span class="control-label">发送对象: </span>{!! $recipients !!}</div>
                    <div>
                        <span class="control-label">发送者: </span>{!! $sender !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-sign-out"> 关闭</i>
            </button>
        </div>
    </div>
</div>