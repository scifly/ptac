<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($major['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $major['id']]) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-minlength' => '4',
                        'data-parsley-maxlength' => '40'
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'school_id',
                'items' => $schools
            ])
            @include('partials.multiple_select', [
                'label' => '包含科目',
                'for' => 'subject_ids',
                'items' => $subjects,
                'selectedItems' => $selectedSubjects
            ])
            <div class="form-group">
                {!! Form::label('remark', '备注',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过255个汉字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-minlength' => '4',
                        'data-parsley-maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', ['enabled' => $major['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
