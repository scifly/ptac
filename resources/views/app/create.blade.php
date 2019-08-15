{!! Form::open([
    'method' => 'post',
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
            @include('shared.single_select', [
                 'label' => '应用类型',
                 'id' => 'category',
                 'items' => $categories,
            ])
            <div class="name form-group" style="display: none;">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-weixin text-green'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'placeholder' => '(不超过12个汉字)',
                            'maxlength' => '12'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="appid form-group">
                {!! Form::label('appid', 'appid', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>ID</strong>
                        </div>
                        {!! Form::text('appid', null, [
                            'class' => 'form-control text-blue',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="appsecret form-group">
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
            <div class="url form-group" style="display: none;">
                {!! Form::label('url', 'url', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa-key'])
                        {!! Form::text('url', null, [
                            'class' => 'form-control text-blue',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="token form-group" style="display: none;">
                {!! Form::label('token', 'token', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa-key'])
                        {!! Form::text('token', null, [
                            'class' => 'form-control text-blue',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="eak form-group" style="display: none;">
                {!! Form::label('encoding_aes_key', 'encoding_aes_key', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa-key'])
                        {!! Form::text('encoding_aes_key', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(如为公众号，此项必填)',
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.remark', [
                'label' => '详情',
                'field' => 'description',
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
{!! Form::close() !!}