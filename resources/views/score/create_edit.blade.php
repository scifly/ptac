<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($score['id']))
                {{ Form::hidden('id', $score['id'], ['id' => 'id']) }}
                {{ Form::hidden('subject', $score['subject_id'], ['id' => 'subject']) }}
                {{ Form::hidden('student', $score['student_id'], ['id' => 'student']) }}
            @endif
            @include('partials.single_select', [
                'label' => '考试名称',
                'id' => 'exam_id',
                'items' => $exams
            ])
            @include('partials.single_select', [
               'label' => '科目名称',
               'id' => 'subject_id',
               'items' => $subjects,
               'icon' => 'fa fa-book'
            ])
            @include('partials.single_select', [
                'label' => '学号',
                'id' => 'student_id',
                'items' => $students,
                'icon' => 'fa fa-child'
            ])
            <div class="form-group">
                {!! Form::label('score', '分数', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('score', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(不超过5个数字含小数点)',
                        'required' => 'true',
                        'type' => "number",
                        'maxlength' => '5',
                    ]) !!}
                </div>
            </div>
            @include('partials.switch', [
                'id' => 'enabled',
                'value' => $score['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
