{!! Form::model($app, [
    'method' => 'put',
    'id' => 'formApp',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            {!! Form::hidden('corp_id', $corpId, ['id' => 'corp_id']) !!}
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    @if ($app['category'] == 1)
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-weixin text-green'])
                            {!! Form::text('name', null, [
                                'class' => 'form-control text-blue',
                                'required' => 'true',
                                'placeholder' => '(不超过12个汉字)',
                                'maxlength' => '12'
                            ]) !!}
                        </div>
                    @else
                        {!! $app['name'] !!}
                    @endif
                </div>
            </div>
            @if ($app['appid'])
                <div class="appid form-group">
                    {!! Form::label('appid', 'appid', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
{{--                        {!! $app['appid'] !!}--}}
                        <div class="input-group" style="width: 100%;">
                            <div class="input-group-addon" style="width: 45px;">
                                <strong>ID</strong>
                            </div>
                            {!! Form::text('appid', null, [
                                'class' => 'form-control text-blue',
                                'disabled' => 'disabled'
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif
            <div class="form-group">
                {!! Form::label('appsecret', 'appsecret', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa-key'])
                        {!! Form::text('appsecret', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'placeholder' => '(必填)',
                        ]) !!}
                    </div>
                </div>
            </div>
            @if (isset($url))
                <div class="form-group">
                    {!! Form::label('url', 'url', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group" style="width: 100%;">
                            @include('shared.icon_addon', ['class' => 'fa-key'])
                            {!! Form::text('url', $url ?? null, [
                                'class' => 'form-control text-blue',
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif
            @if (isset($token))
                <div class="form-group" style="display: none;">
                    {!! Form::label('token', 'token', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group" style="width: 100%;">
                            @include('shared.icon_addon', ['class' => 'fa-key'])
                            {!! Form::text('token', $token ?? null, [
                                'class' => 'form-control text-blue',
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif
            @if (isset($encoding_aes_key))
                <div class="eak form-group" style="display: none;">
                    {!! Form::label('encoding_aes_key', 'encoding_aes_key', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group" style="width: 100%;">
                            @include('shared.icon_addon', ['class' => 'fa-key'])
                            {!! Form::text('encoding_aes_key', $encoding_aes_key ?? null, [
                                'class' => 'form-control text-blue',
                                'placeholder' => '(如为公众号，此项必填)',
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif
            @include('shared.remark', [
                'label' => '详情',
                'field' => 'description',
            ])
            @if ($app['category'] == 1)
                <div class="form-group">
                    {!! Form::label('redirect_domain', '企业应用可信域名', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-location-arrow text-purple'])
                            {!! Form::text('redirect_domain', $redirect_domain ?? null, [
                                'class' => 'form-control text-blue',
                                'maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('home_url', '主页型应用url', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-link'])
                            {!! Form::text('home_url', $home_url ?? null, [
                                'class' => 'form-control text-blue',
                                'maxlength' => '255'
                            ]) !!}
                        </div>
                    </div>
                </div>
                @include('shared.switch', [
                    'label' => '打开地理位置上报',
                    'id' => 'report_location_flag',
                    'value' => $report_location_flag ?? null,
                    'options' => ['是', '否']
                ])
                @include('shared.switch', [
                    'label' => '上报用户进入应用事件',
                    'id' => 'isreportenter',
                    'value' => $isreportenter ?? null,
                    'options' => ['是', '否']
                ])
                @include('shared.switch', [
                    'id' => 'enabled',
                    'value' => $app['enabled'] ?? null
                ])
            @endif
        </div>
    </div>
    @include('shared.form_buttons')
</div>
{!! Form::close() !!}