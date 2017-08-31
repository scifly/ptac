<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($subject['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $subject['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过20个汉字',
                        'data-parsley-required' => 'true',
                        'data-parsley-maxlength' => '20',
                        'data-parsley-minlength' => '2',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('max_score', '最高分',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('max_score', null, [
                        'class' => 'form-control',
                        'placeholder' => '最高分必须是数字',
                        'data-parsley-required' => 'true',
                        'data-parsley-type' => 'integer',
                        'data-parsley-maxlength' => '3',
                        'data-parsley-minlength' => '3',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('pass_score', '及格分', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('pass_score', null, [
                        'class' => 'form-control',
                        'placeholder' => '及格分必须是数字',
                        'data-parsley-required' => 'true',
                        'data-parsley-type' => 'integer',
                        'data-parsley-maxlength' => '2',
                        'data-parsley-minlength' => '2',
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
                'for' => 'grade_ids',
                'items' => $grades,
                'selectedItems' => isset($selectedGrades) ? $selectedGrades : NULL
            ])
            @include('partials.multiple_select', [
                'label' => '包含专业',
                'for' => 'major_ids',
                'items' => $majors,
                'selectedItems' => isset($selectedMajors) ? $selectedMajors : NULL
            ])
            @include('partials.enabled', [
                'label' => '是否为副科',
                'for' => 'isaux',
                'value' => $subject['isaux']
            ])
            @include('partials.enabled', [
                'label' => '是否启用',
                'for' => 'enabled',
                'value' => $subject['enabled']
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
