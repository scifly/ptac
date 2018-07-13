<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                &times;
            </button>
            <h4 class="modal-title">
                {!! $msgTitle; !!}
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-xs-2">应用: </label>
                        <span>
                            @if ($app)
                                <img style="height: 16px; vertical-align: sub;" alt="" src="{!! $app['square_logo_url'] !!}"/>
                                {!! $app['name'] !!}
                            @else
                                未知
                            @endif
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-2">发送时间: </label>
                        <span>{!! $sentAt !!}</span>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-2">发送对象: </label>
                        <span>{!! $recipients !!}</span>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-2">发送者: </label>
                        <span>{!! $sender !!}</span>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-2">消息内容: </label>
                        {!! $msgBody !!}
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