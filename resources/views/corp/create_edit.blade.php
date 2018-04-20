<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($corp['id']))
                {{ Form::hidden('id', $corp['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', [
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
                {!! Form::label('acronym', '缩写', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', [
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
            @if (Auth::user()->group->name == '运营')
                @include('partials.single_select', [
                    'label' => '所属运营者',
                    'id' => 'company_id',
                    'items' => $companies,
                    'icon' => 'fa fa-building text-blue'
                ])
            @endif
            <div class="form-group">
                {!! Form::label('corpid', '企业号ID', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i style="width: 20px;">ID</i>
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
                {!! Form::label('contact_sync_secret', '"通讯录同步"应用Secret', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('contact_sync_secret', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(43个小写字母与阿拉伯数字)',
                        'required' => 'true',
                        'data-parsley-length' => '[43, 43]'
                    ]) !!}
                </div>
            </div>
            @if (isset($corp['department_id']))
                {!! Form::hidden('department_id', $corp['department_id']) !!}
            @endif
            @if (isset($corp['menu_id']))
                {!! Form::hidden('menu_id', $corp['menu_id']) !!}
            @endif
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $corp['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
