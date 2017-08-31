{{ Form::hidden('parent_id', null, ['id' => 'parent_id']) }}
<div class="form-group">
    {!! Form::label('name', '部门名称',[
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6">
        {!! Form::text('name', null, [
            'class' => 'form-control special-form-control',
            'placeholder' => '(请输入部门名称)',
            'data-parsley-required' => 'true',
            'data-parsley-maxlength' => '255'
        ]) !!}

    </div>
</div>
<div class="form-group">
    {!! Form::label('remark', '备注', [
        'class' => 'col-sm-3 control-label'
    ]) !!}
    <div class="col-sm-6">
        {!! Form::text('remark', null, [
            'class' => 'form-control special-form-control',
            'placeholder' => '(请输入备注)',
            'data-parsley-required' => 'true',
            'data-parsley-maxlength' => '255'
        ]) !!}
    </div>
</div>
@include('partials.single_select', [
    'label' => '所属企业',
    'id' => 'corp_id',
    'items' => $corps
])
@include('partials.single_select', [
    'label' => '所属学校',
    'id' => 'school_id',
    'items' => $schools
])
@include('partials.enabled', [
    'label' => '是否启用',
    'for' => 'enabled',
    'value' => $department['enabled']
])
{!! Form::hidden('id') !!}
@include('partials.form_buttons')
