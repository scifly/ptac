<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <a href="javascript:" class="btn btn-primary">
                    <i class="fa fa-mail-reply"></i>
                    返回列表
                </a>
            </div>
            <div class="box-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! Form::label('name', '应用名称',['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('name', null, [
                                'class' => 'form-control special-form-control',
                                'data-parsley-required' => 'true',
                                'placeholder' => '请输入应用名称（不超过12个汉字）',
                                'data-parsley-maxlength' => '12'
                            ]) !!}

                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('description', '应用备注',['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('description', null, [
                                'class' => 'form-control  special-form-control',
                                'data-parsley-required' => 'true',
                                'placeholder' => '请输入备注',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('agentid', '应用id',['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('agentid', null, [
                                'class' => 'form-control  special-form-control',
                                'data-parsley-required' => 'true',
                                'data-parsley-type' => 'integer',
                                'placeholder' => '请输入应用id（不超过3位的数字）',
                                'data-parsley-maxlength' => '3'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('url', '推送请求的访问协议和地址',['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('url', null, [
                                'class' => 'form-control  special-form-control',
                                'data-parsley-required' => 'true',
                                'placeholder' => '请输入推送请求的访问协议和地址',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('token', '用于生成签名的token',['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('token', null, [
                                'class' => 'form-control  special-form-control',
                                'data-parsley-required' => 'true',
                                'placeholder' => '请输入token',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('encodingaeskey', '消息体的加密',['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('encodingaeskey', null, [
                                'class' => 'form-control  special-form-control',
                                'data-parsley-required' => 'true',
                                'placeholder' => '请输入消息体的加密',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('logo_mediaid', '企业应用头像的mediaid',['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('logo_mediaid', null, [
                                'class' => 'form-control  special-form-control',
                                'data-parsley-required' => 'true',
                                'placeholder' => '请输入企业应用头像的mediaid',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('redirect_domain', '企业应用可信域名',['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('redirect_domain', null, [
                                'class' => 'form-control  special-form-control',
                                'data-parsley-required' => 'true',
                                'placeholder' => '请输入企业应用可信域名',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('home_url', '主页型应用url',['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('home_url', null, [
                                'class' => 'form-control  special-form-control',
                                'data-parsley-required' => 'true',
                                'placeholder' => '请输入主页型应用url',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('chat_extension_url', '关联会话url',['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('chat_extension_url', null, [
                                'class' => 'form-control  special-form-control',
                                'data-parsley-required' => 'true',
                                'placeholder' => '请输入关联会话url',
                                'data-parsley-maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('menu', '应用菜单',['class' => 'col-sm-3 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('menu', null, [
                                'class' => 'form-control  special-form-control',
                                'data-parsley-required' => 'true',
                                'placeholder' => '请输入应用菜单',
                                'data-parsley-maxlength' => '1024'
                            ]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="report_location_flag" class="col-sm-3 control-label">是否打开地理位置上报</label>
                        <div class="col-sm-3" style="padding-top: 5px;">
                            <input type="checkbox" name="report_location_flag" id="report_location_flag" class="js-switch" @if(isset($app))@if($app['report_location_flag'] === 1)checked @endif @else checked @endif>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="isreportuser" class="col-sm-3 control-label">是否接收用户变更通知</label>
                        <div class="col-sm-3" style="padding-top: 5px;">
                            <input type="checkbox" name="isreportuser" id="isreportuser" class="js-switch"  @if(isset($app))@if($app['isreportuser'] === 1)checked @endif @else checked @endif>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="isreportenter" class="col-sm-3 control-label">是否上报用户进入应用事件</label>
                        <div class="col-sm-3" style="padding-top: 5px;">
                            <input type="checkbox" name="isreportenter" id="isreportenter" class="js-switch"  @if(isset($app))@if($app['isreportenter'] === 1)checked @endif @else checked @endif>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="enabled" class="col-sm-3 control-label">启用</label>
                        <div class="col-sm-3" style="padding-top: 5px;">
                            <input type="checkbox" name="enabled" id="enabled" class="form-control js-switch"  @if(isset($app))@if($app['enabled'] === 1)checked @endif @else checked @endif>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                {{--button--}}
                <div class="form-group">
                    <div class="col-sm-3 col-sm-offset-2" style="padding: 0;">
                        {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left','id' =>'save']) !!}
                        {!! Form::reset('取消', ['class' => 'btn btn-default pull-right','id' =>'cencel']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
