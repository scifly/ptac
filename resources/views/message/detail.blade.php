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
                        <img style="height: 16px;" alt="" src="{!! $app['square_logo-url'] !!}"/>
                        {!! $app['name'] !!}
                    </div>
                    <div>发送时间: {!! $sentAt !!}</div>
                    <div>发送对象: {!! $recipients !!}</div>
                    <div>发送者: {!! $sender !!}</div>
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
<dl class="dl-horizontal">
<dt>地址：</dt>
<dd>5039 Esteban Squares Apt. 714 Nadiamouth, LA 883765039 Esteban Squares Apt. 714
Nadiamouth, LA 88376
</dd>
<dt>类型：</dt>
<dd>Zachariah Kozey</dd>
<dt>所属企业：</dt>
<dd>成都凌凯通信技术</dd>
<dt>创建于：</dt>
<dd>2 days ago</dd>
<dt>更新于：</dt>
<dd>2 days ago</dd>
<dt>状态：</dt>
<dd><span class="badge bg-green">已启用</span></dd>
</dl>
