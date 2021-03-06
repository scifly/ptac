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
            {!! Form::hidden('id', $app['id']) !!}
            {!! Form::hidden('corp_id', $corpId) !!}
            {!! Form::hidden('category', $app['category']) !!}
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', [
                            'class' => 'fa-weixin text-green'
                        ])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'disabled' => $app['category'] != 1,
                            'placeholder' => '(必填。不得超过12个汉字)',
                            'maxlength' => '12'
                        ]) !!}
                    </div>
                </div>
            </div>
            @if ($app['appid'])
                <div class="form-group">
                    @include('shared.label', ['field' => 'appid', 'label' => 'appid'])
                    <div class="col-sm-6">
                        <div class="input-group" style="width: 100%;">
                            <div class="input-group-addon" style="width: 45px;">
                                <strong>ID</strong>
                            </div>
                            {!! Form::text('appid', null, [
                                'class' => 'form-control text-blue',
                                'disabled' => true,
                                'placeholder' => '(必填)'
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif
            <div class="form-group">
                @include('shared.label', ['field' => 'appsecret', 'label' => 'appsecret'])
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
                    @include('shared.label', ['field' => 'url', 'label' => 'url'])
                    <div class="col-sm-6">
                        <div class="input-group" style="width: 100%;">
                            @include('shared.icon_addon', ['class' => 'fa-link'])
                            {!! Form::text('url', $url ?? null, [
                                'class' => 'form-control text-blue',
                                'placeholder' => '(必填)'
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif
            @if (isset($token))
                <div class="form-group">
                    @include('shared.label', ['field' => 'token', 'label' => 'token'])
                    <div class="col-sm-6">
                        <div class="input-group" style="width: 100%;">
                            @include('shared.icon_addon', ['class' => 'fa-key'])
                            {!! Form::text('token', $token ?? null, [
                                'class' => 'form-control text-blue',
                                'placeholder' => '(必填)'
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif
            @if (isset($encoding_aes_key))
                <div class="form-group">
                    @include('shared.label', ['field' => 'encoding_aes_key', 'label' => 'encoding_aes_key'])
                    <div class="col-sm-6">
                        <div class="input-group" style="width: 100%;">
                            @include('shared.icon_addon', ['class' => 'fa-key'])
                            {!! Form::text('encoding_aes_key', $encoding_aes_key ?? null, [
                                'class' => 'form-control text-blue',
                                'placeholder' => '(必填)',
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif
            @if (isset($type))
                <div class="form-group">
                    @include('shared.label', ['field' => 'type', 'label' => '公众号类型'])
                    <div class="col-sm-6">
                        <div class="input-group" style="width: 100%;">
                            <span>{!! $type ? '服务号' : '订阅号' !!}</span>
                            {!! Form::hidden('type', $type, ['id' => 'type']) !!}
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
                    @include('shared.label', ['field' => 'redirect_domain', 'label' => '企业应用可信域名'])
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-location-arrow text-purple'])
                            {!! Form::text('redirect_domain', $redirect_domain ?? null, [
                                'class' => 'form-control text-blue',
                                'maxlength' => '255',
                                'placeholder' => '(必填)'
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    @include('shared.label', ['field' => 'home_url', 'label' => '主页型应用url'])
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-link'])
                            {!! Form::text('home_url', $home_url ?? null, [
                                'class' => 'form-control text-blue',
                                'maxlength' => '255',
                                'placeholder' => '(必填)'
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

                    'value' => $app['enabled'] ?? null
                ])
            @endif
        </div>
    </div>
    @include('shared.form_buttons')
</div>
{!! Form::close() !!}