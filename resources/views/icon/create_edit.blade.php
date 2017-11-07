<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($icon['id']))
                {{ Form::hidden('id', $icon['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', 'Icon名称',[
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入功能名称)',
                        'required' => 'true',
                        'maxlength' => '80'
                    ]) !!}

                </div>
            </div>
            @include('partials.single_select', [
                'label' => 'icon类型',
                'id' => 'icon_type_id',
                'items' => $iconTypes
            ])
            <div class="form-group">
                {!! Form::label('remark', '备注', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入备注)',
                        'required' => 'true',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => isset($icon['enabled']) ? $icon['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>