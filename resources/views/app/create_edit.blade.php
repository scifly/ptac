<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', '应用名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('description', '应用备注',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('description', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('agentid', '应用id',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('agentid', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('url', '推送请求的访问协议和地址',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('url', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('token', '用于生成签名的token',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('token', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('encodingaeskey', '消息体的加密',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('encodingaeskey', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">
                    是否打开地理位置上报
                </label>
                <div class="col-sm-2" style="padding-top: 7px;">
                    {!! Form::radio('report_location_flag', '1', true) !!}
                    {!! Form::label('report_location_flag', '打开') !!}
                    {!! Form::radio('report_location_flag', '0') !!}
                    {!! Form::label('report_location_flag', '禁用') !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('logo_mediaid', '企业应用头像的mediaid',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('logo_mediaid', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('redirect_domain', '企业应用可信域名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('redirect_domain', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">
                    是否接收用户变更通知
                </label>
                <div class="col-sm-2" style="padding-top: 7px;">
                    {!! Form::radio('isreportuser', '1', true) !!}
                    {!! Form::label('isreportuser', '接收') !!}
                    {!! Form::radio('isreportuser', '0') !!}
                    {!! Form::label('isreportuser', '禁用') !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">
                    是否上报用户进入应用事件
                </label>
                <div class="col-sm-2" style="padding-top: 7px;">
                    {!! Form::radio('isreportenter', '1', true) !!}
                    {!! Form::label('isreportenter', '接收') !!}
                    {!! Form::radio('isreportenter', '0') !!}
                    {!! Form::label('isreportenter', '禁用') !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('home_url', '主页型应用url',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('home_url', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('chat_extension_url', '关联会话url',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('chat_extension_url', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('menu', '应用菜单',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('menu', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-4">
                    {!! Form::radio('enabled', '1', true) !!}
                    {!! Form::label('enabled', '启用') !!}
                    {!! Form::radio('enabled', '0') !!}
                    {!! Form::label('enabled', '禁用') !!}
                </div>
            </div>

        </div>
    </div>
    <div class="box-footer">
        {{--button--}}
        <div class="form-group">
            <div class="col-sm-3 col-sm-offset-4">
                {!! Form::button('保存', ['class' => 'btn btn-primary pull-left','id' =>'save']) !!}
                {!! Form::reset('重置', ['class' => 'btn btn-default pull-right','id' =>'cencel']) !!}
            </div>
        </div>
    </div>
</div>
