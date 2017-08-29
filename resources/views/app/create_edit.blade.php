<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($app['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $tab['id']]) }}
            @endif
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
            @include('partials.enabled', ['enabled' => $app['enabled'], 'label' =>'是否打开地理位置上报'])
            @include('partials.enabled', ['enabled' => $app['enabled'], 'label' => '是否接收用户变更通知'])
            {{--<div class="form-group">--}}
            {{--<label for="isreportenter" class="col-sm-3 control-label">--}}
            {{--是否上报用户进入应用事件--}}
            {{--</label>--}}
            {{--<div class="col-sm-3" style="padding-top: 5px;">--}}
            {{--<input id="isreportenter" type="checkbox" name="isreportenter" data-render="switchery"--}}
            {{--data-theme="default" data-switchery="true"--}}
            {{--@if(!empty($app['enabled'])) checked @endif--}}
            {{--data-classname="switchery switchery-small"/>--}}
            {{--</div>--}}
            {{--</div>--}}
            @include('partials.enabled', ['enabled' => $app['enabled'], 'label' => '是否上报用户进入应用事件'])
            @include('partials.enabled', ['enabled' => $app['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>