<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($school['id']))
                {{ Form::hidden('id', $school['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称',[
                    'class' => 'col-sm-3 control-label',
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', [
                            'class' => 'fa-university text-purple'
                        ])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'data-parsley-length' => '[6, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('address', '地址', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>地</strong>
                        </div>
                        {!! Form::text('address', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'data-parsley-length' => '[6, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('signature', '签名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>签</strong>
                        </div>
                        {!! Form::text('signature', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'placeholder'=>'签名格式：【内容】',
                            'data-parsley-length' => '[2, 7]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @if (in_array(Auth::user()->group->name, ['运营', '企业']))
                @include('shared.single_select', [
                    'label' => '学校类型',
                    'id' => 'school_type_id',
                    'items' => $schoolTypes
                ])
            @endif
            @if (Auth::user()->group->name == '运营')
                @include('shared.single_select', [
                    'label' => '所属企业',
                    'id' => 'corp_id',
                    'items' => $corps,
                    'icon' => 'fa fa-weixin text-green'
                ])
                @include('shared.multiple_select', [
                    'label' => '第三方同步接口',
                    'id' => 'user_ids',
                    'icon' => 'fa fa-link',
                    'items' => $apis,
                    'selectedItems' => $selectedApis ?? null,
                ])
            @endif

            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $school['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
