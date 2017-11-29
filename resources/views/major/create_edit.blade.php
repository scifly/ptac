<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($major['id']))
                {{ Form::hidden('id', $major['id'], ['id' => 'id']) }}
            @endif
            {{ Form::hidden('school_id', $schoolId, ['id' => 'school_id']) }}
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 40]',
                    ]) !!}
                </div>
            </div>
            @include('partials.multiple_select', [
                'label' => '包含科目',
                'id' => 'subject_ids',
                'items' => $subjects,
                'selectedItems' => isset($selectedSubjects) ? $selectedSubjects : NULL
            ])
            @include('partials.remark')
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => isset($major['enabled']) ? $major['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
