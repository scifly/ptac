{!! Form::model($app, ['method' => 'put', 'id' => 'formApp', 'data-parsley-validate' => 'true']) !!}
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($app['id']))
                {{ Form::hidden('id', $app['id'], ['id' => 'id']) }}
            @endif
            @if (!empty($app['corp_id']))
                {{ Form::hidden('corp_id', $app['corp_id'], ['id' => 'corp_id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('agentid', '应用id', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('agentid', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'placeholder' => '请输入应用id',
                        'maxlength' => '12'
                    ]) !!}

                </div>
            </div>
            <div class="form-group">
                {!! Form::label('name', '应用名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'placeholder' => '请输入应用名称（不超过12个汉字）',
                        'maxlength' => '12'
                    ]) !!}

                </div>
            </div>
            <div class="form-group">
                {!! Form::label('description', '应用详情', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('description', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'placeholder' => '请输入应用详情',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('redirect_domain', '企业应用可信域名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('redirect_domain', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'placeholder' => '请输入企业应用可信域名',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('home_url', '主页型应用url', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('home_url', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'placeholder' => '请输入主页型应用url',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '是否打开地理位置上报',
                'id' => 'report_location_flag',
                'value' => isset($app['report_location_flag']) ? $app['report_location_flag'] : NULL
            ])
            @include('partials.enabled', [
                'label' => '是否上报用户进入应用事件',
                'id' => 'isreportenter',
                'value' => isset($app['isreportenter']) ? $app['isreportenter'] : NULL,
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
{!! Form::close() !!}