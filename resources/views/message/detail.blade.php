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
                        {!! Form::label('', '应用：', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6" style="margin-top: 7px;">
                            @if ($app)
                                <img style="height: 16px; vertical-align: sub;"
                                     alt="" src="{!! $app['square_logo_url'] !!}"
                                     class="img-circle"
                                />
                                {!! $app['name'] !!}
                            @else
                                未知
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('', '发送时间：', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6" style="margin-top: 7px;">
                            <span class="badge bg-blue">{!! $sentAt !!}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('', '发送对象：', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6" style="margin-top: 7px;">{!! $recipients !!}</div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('', '发送者：', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6" style="margin-top: 7px;">
                            <span class="badge bg-green">{!! $sender !!}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('', '消息内容：', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6" style="margin-top: 7px;">{!! $msgBody !!}</div>
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