<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($exam) && !empty($exam['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $exam['id']]) }}
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
            <div class="form-group">
                {!! Form::label('remark', '备注',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('remark', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能超过20个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-minlength' => '2',
                    'data-parsley-maxlength' => '20'
                    ]) !!}
                </div>
            </div>
                @include('partials.single_select', [
                    'label' => '所属考试类型',
                    'id' => 'exam_type_id',
                    'items' => $examtypes
                ])
                @include('partials.multiple_select', [
                    'label' => '所属班级',
                    'for' => 'class_ids',
                    'items' => $classes,
                    'selectedItems' => isset($selectedClasses) ? $selectedClasses : []
                ])
                @include('partials.multiple_select', [
                    'label' => '科目',
                    'for' => 'subject_ids',
                    'items' => $subjects,
                    'selectedItems' => isset($selectedSubjects) ? $selectedSubjects : []
                ])

            <div class="form-group">
                {!! Form::label('max_scores', '科目满分',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('max_scores', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过10个数字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-type' => "number",
                        'data-parsley-minlength' => '1',
                        'data-parsley-maxlength' => '10'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('pass_scores', '科目及格分数',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('pass_scores', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过10个数字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-type' => "number",
                        'data-parsley-minlength' => '1',
                        'data-parsley-maxlength' => '10'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('start_date', '考试开始日期',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::date('start_date', null, [
                        'class' => 'form-control',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('end_date', '考试结束日期',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::date('end_date', null, [
                        'class' => 'form-control',
                    ]) !!}
                </div>
            </div>

            @include('partials.enabled', ['enabled' => isset($exam['enabled']) ? $exam['enabled'] : ""])


        </div>
    </div>
    @include('partials.form_buttons')
</div>
