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
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-graduation-cap"></i>
                        </div>
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 40]',
                    ]) !!}
                    </div>
                </div>
            </div>
            @include('partials.multiple_select', [
                'label' => '包含科目',
                'id' => 'subject_ids',
                'items' => $subjects,
                'selectedItems' => $selectedSubjects ?? NULL,
                'icon' => 'fa fa-book'
            ])
            @include('partials.remark')
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $major['enabled'] ?? NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
