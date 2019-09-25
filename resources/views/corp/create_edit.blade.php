<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($corp['id']))
                {!! Form::hidden('id', $corp['id']) !!}
                {!! Form::hidden('department_id', $corp['department_id']) !!}
                {!! Form::hidden('menu_id', $corp['menu_id']) !!}
            @endif
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', [
                            'class' => 'fa-weixin text-green'
                        ])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不超过60个汉字)',
                            'required' => 'true',
                            'minlength' => '3',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                @include('shared.label', ['field' => 'acronym', 'label' => '缩写'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', [
                            'class' => 'fa-weixin text-green'
                        ])
                        {!! Form::text('acronym', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不超过20个小写字母)',
                            'required' => 'true',
                            'minlength' => '3',
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.single_select', [
                'label' => '所属运营者',
                'id' => 'company_id',
                'items' => $companies,
                'icon' => 'fa fa-building text-blue'
            ])
            <div class="form-group">
                @include('shared.label', ['field' => 'corpid', 'label' => '企业ID'])
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>ID</strong>
                        </div>
                        {!! Form::text('corpid', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(18个小写字母与阿拉伯数字)',
                            'required' => 'true',
                            'data-parsley-type' => 'alphanum',
                            'data-parsley-length' => '[18, 18]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                @include('shared.label', ['field' => 'departmentid', 'label' => '根部门id'])
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa-sitemap'])
                        {!! Form::text('departmentid', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请从企业微信后台复制粘贴到此处)',
                            'type' => 'number'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                @include('shared.label', ['field' => 'mchid', 'label' => '商户号'])
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>商</strong>
                        </div>
                        {!! Form::text('mchid', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请从微信商户平台复制粘贴到此处)',
                            'data-parsley-length' => '[0, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                @include('shared.label', ['field' => 'apikey', 'label' => '商户支付密钥'])
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        @include('shared.icon_addon', ['class' => 'fa-key'])
                        {!! Form::text('apikey', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请从微信商户平台复制粘贴到此处)',
                            'data-parsley-length' => '[0, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.switch', [

                'value' => $corp['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
