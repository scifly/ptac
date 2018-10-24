<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($procedure['id']))
                {{ Form::hidden('id', $procedure['id'], ['id' => 'id']) }}
            @endif
            @include('partials.single_select', [
                'label' => '流程类型',
                'id' => 'procedure_type_id',
                'items' => $procedureTypes
            ])
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(不得超过20个汉字)',
                        'required' => 'true',
                        'maxlength' => '60'
                    ]) !!}
                </div>
            </div>
            @include('partials.remark')
            @include('partials.switch', [
                'id' => 'enabled',
                'value' => $procedure['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
