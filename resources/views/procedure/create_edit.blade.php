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
            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'school_id',
                'items' => $schools
            ])
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过20个汉字)',
                        'required' => 'true',
                        'maxlength' => '60'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注',[
                    'class' => 'col-sm-3 control-label',
                ]) !!}
                <div class="col-sm-3">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control',
                         'placeholder' => '(不得超过80个汉字)',
                         'required' => 'true',
                         'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '状态',
                'id' => 'enabled',
                'value' => isset($procedure['enabled']) ? $procedure['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
