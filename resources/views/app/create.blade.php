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
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-weixin text-green'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(必填。不得超过12个汉字)',
                            'maxlength' => '12'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="appid form-group">
                @include('shared.label', ['field' => 'appid', 'label' => 'appid'])
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>ID</strong>
                        </div>
                        {!! Form::text('appid', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(必填)'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="appsecret form-group">
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
            <div class="url form-group" style="display: none;">
                @include('shared.label', ['field' => 'url', 'label' => 'url'])
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa-link'])
                        {!! Form::text('url', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(必填)'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="token form-group" style="display: none;">
                @include('shared.label', ['field' => 'token', 'label' => 'token'])
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa-key'])
                        {!! Form::text('token', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(必填)'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="eak form-group" style="display: none;">
                @include('shared.label', ['field' => 'encoding_aes_key', 'label' => 'encoding_aes_key'])
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
            <div class="type form-group">
                @include('shared.label', ['field' => 'type', 'label' => '公众号类型'])
                <div class="col-sm-6" style="padding-top: 5px;">
                    {!! Form::radio('type', 1, true, ['id' => 'type1', 'class' => 'minimal']) !!}
                    {!! Form::label('type1', '服务号', ['class' => 'switch-lbl']) !!}
                    {!! Form::radio('type', 0, false, ['id' => 'type2', 'class' => 'minimal']) !!}
                    {!! Form::label('type2', '订阅号', ['class' => 'switch-lbl']) !!}
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