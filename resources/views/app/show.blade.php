<div class="modal fade" id="modal-show-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    应用详情
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <dl class="dl-horizontal">
                            <dt>应用名称：{{ $app->name }}</dt>
                            <dt>应用备注：{{ $app->description }}</dt>
                            <dt>应用id：{{ $app->agentid }}</dt>
                            <dt>推送请求的访问协议和地址：{{ $app->url }}</dt>
                            <dt>用于生成签名的token：{{ $app->token }}</dt>
                            <dt>消息体的加密：{{ $app->encodingaeskey }}</dt>
                            <dt>是否打开地理位置上报：{{ $app->report_location_flag==1 ? '是' : '否' }}</dt>
                            <dt>企业应用头像的mediaid：{{ $app->logo_mediaid }}</dt>
                            <dt>企业应用可信域名：{{ $app->redirect_domain }}</dt>
                            <dt>是否接收用户变更通知：{{ $app->isreportuser==1 ? '是' : '否' }}</dt>
                            <dt>是否上报用户进入应用事件：{{ $app->isreportenter==1 ? '是' : '否' }}</dt>
                            <dt>主页型应用url：{{ $app->home_url }}</dt>
                            <dt>关联会话url：{{ $app->chat_extension_url }}</dt>
                            <dt>应用菜单：{{ $app->menu }}</dt>
                            <dt>是否启用：{{ $app->enabled==1 ? '是' : '否' }}</dt>
                            <dt>Last edited: {{ $app->updated_at->diffForHumans() }}</dt>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                </button>
            </div>
        </div>
    </div>
</div>
