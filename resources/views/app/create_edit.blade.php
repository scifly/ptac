<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($app['id']))
                {{ Form::hidden('id', $app['id'], ['id' => 'id']) }}
            @endif
            {!! Form::hidden('corp_id', $corpId, ['id' => 'corp_id']) !!}
            @if (empty($app['id']))
                @include('shared.single_select', [
                     'label' => '应用类型',
                     'id' => 'category',
                     'items' => $categories,
                 ])
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-weixin text-green'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'placeholder' => '(必填，不超过12个汉字)',
                            'maxlength' => '12'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('appid', 'agentid/appid', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>ID</strong>
                        </div>
                        {!! Form::text('agentid', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'placeholder' => '(必填)',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('secret', 'secret', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa-key'])
                        {!! Form::text('secret', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'placeholder' => '请输入应用secret / 公众号appsecret',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('token', 'token', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa-key'])
                        {!! Form::text('token', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(如为公众号，此项必填)',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('encoding_aes_key', 'encoding_aes_key', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa-key'])
                        {!! Form::text('encoding_aes_key', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(必填)',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('redirect_domain', '企业应用可信域名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-location-arrow text-purple'])
                        {!! Form::text('redirect_domain', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(选填)',
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
                        {!! Form::text('home_url', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(必填)',
                            'maxlength' => '255'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.remark', [
                'label' => '应用详情',
                'field' => 'description',
            ])
            @include('shared.switch', [
                'label' => '打开地理位置上报',
                'id' => 'report_location_flag',
                'value' => $app['report_location_flag'] ?? null,
                'options' => ['是', '否']
            ])
            @include('shared.switch', [
                'label' => '上报用户进入应用事件',
                'id' => 'isreportenter',
                'value' => $app['isreportenter'] ?? null,
                'options' => ['是', '否']
            ])
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $app['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>