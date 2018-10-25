<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($icon['id']))
                {{ Form::hidden('id', $icon['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称',[
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入图标CSS类名称)',
                        'required' => 'true',
                        'maxlength' => '80'
                    ]) !!}

                </div>
            </div>
            @include('shared.single_select', [
                'label' => 'icon类型',
                'id' => 'icon_type_id',
                'items' => $iconTypes
            ])
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $icon['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>