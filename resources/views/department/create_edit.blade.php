<div class="box">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($department['id']))
                {{ Form::hidden('id', $department['id'], ['id' => 'id']) }}
            @endif
            {{ Form::hidden('parent_id', isset($parentId) ? $parentId : null, ['id' => 'parent_id']) }}
            <div class="form-group">
                {!! Form::label('name', '部门名称',[
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入部门名称)',
                        'required' => 'true',
                        'maxlength' => '255'
                    ]) !!}

                </div>
            </div>
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
            {!! Form::hidden(
                'department_type_id',
                isset($departmentTypeId) ? $departmentTypeId : null,
                ['id' => 'department_type_id']
            ) !!}
            @include('partials.enabled', [
                'label' => '状态',
                'id' => 'enabled',
                'value' => isset($department['enabled']) ? $department['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>

