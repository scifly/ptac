{!! Form::model($app, [
    'method' => 'put',
    'id' => 'formApp',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($app['id']))
                {{ Form::hidden('id', $app['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('agentid', 'agentid', [
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
                            'placeholder' => '请输入应用id',
                            'maxlength' => '12'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-weixin text-green'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'placeholder' => '请输入应用名称（不超过12个汉字）',
                            'maxlength' => '12'
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
                        @include('partials.icon_addon', ['class' => 'fa-location-arrow text-purple'])
                        {!! Form::text('redirect_domain', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'placeholder' => '请输入企业应用可信域名',
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
                        @include('partials.icon_addon', ['class' => 'fa-link'])
                        {!! Form::text('home_url', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'placeholder' => '请输入主页型应用url',
                            'maxlength' => '255'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('partials.remark', [
                'label' => '应用详情',
                'field' => 'description',
            ])
            @include('partials.switch', [
                'label' => '打开地理位置上报',
                'id' => 'report_location_flag',
                'value' => $app['report_location_flag'] ?? null,
                'options' => ['是', '否']
            ])
            @include('partials.switch', [
                'label' => '上报用户进入应用事件',
                'id' => 'isreportenter',
                'value' => $app['isreportenter'] ?? null,
                'options' => ['是', '否']
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
{!! Form::close() !!}