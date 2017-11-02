<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($subject['id']))
                {{ Form::hidden('id', $subject['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过20个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 20]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('max_score', '最高分', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('max_score', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过6个数字含小数点)',
                        'required' => 'true',
                        'type' => 'number',
                        'data-parsley-length' => '[3, 6]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('pass_score', '及格分', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('pass_score', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过5个数字含小数点)',
                        'required' => 'true',
                        'type' => 'number',
                        'data-parsley-length' => '[2, 5]'
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'school_id',
                'items' => $schools
            ])
            @include('partials.multiple_select', [
                'label' => '所属年级',
                'id' => 'grade_ids',
                'items' => $grades,
                'selectedItems' => isset($selectedGrades) ? $selectedGrades : NULL
            ])
            @include('partials.multiple_select', [
                'label' => '包含专业',
                'id' => 'major_ids',
                'items' => $majors,
                'selectedItems' => isset($selectedMajors) ? $selectedMajors : NULL
            ])
            @include('partials.enabled', [
                'label' => '是否为副科',
                'id' => 'isaux',
                'value' => isset($subject['isaux']) ? $subject['isaux'] : NULL
            ])
            @include('partials.enabled', [
                'label' => '状态',
                'id' => 'enabled',
                'value' => isset($subject['enabled']) ? $subject['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
