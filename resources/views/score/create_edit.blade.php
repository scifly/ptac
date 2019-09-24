<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($score))
                {!! Form::hidden('id', $score['id']) !!}
                {!! Form::hidden('subject', $score['subject_id'], ['id' => 'subject']) !!}
                {!! Form::hidden('student', $score['student_id'], ['id' => 'student']) !!}
            @endif
            @include('shared.single_select', [
                'label' => '考试名称',
                'id' => 'exam_id',
                'items' => $exams
            ])
            @include('shared.single_select', [
               'label' => '科目名称',
               'id' => 'subject_id',
               'items' => $subjects,
               'icon' => 'fa fa-book'
            ])
            @include('shared.single_select', [
                'label' => '学号',
                'id' => 'student_id',
                'items' => $students,
                'icon' => 'fa fa-child'
            ])
            <div class="form-group">
                @include('shared.label', ['field' => 'score', 'label' => '分数'])
                <div class="col-sm-6">
                    {!! Form::text('score', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(不超过5位数字，含小数点)',
                        'required' => 'true',
                        'type' => "number",
                        'maxlength' => '5',
                    ]) !!}
                </div>
            </div>
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $score['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
