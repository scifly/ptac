<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            {{--<div class="form-group">--}}
                {{--{!! Form::label('student_id', '学号',['class' => 'col-sm-4 control-label']) !!}--}}
                {{--<div class="col-sm-3">--}}
                    {{--{!! Form::select('student_id', $students, null, ['class' => 'form-control']) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '学号',
                'id' => 'student_id',
                'items' => $students
            ])

            {{--<div class="form-group">--}}
                {{--{!! Form::label('subject_id', '科目名称',['class' => 'col-sm-4 control-label']) !!}--}}
                {{--<div class="col-sm-3">--}}
                    {{--{!! Form::select('subject_id', $subjects, null, ['class' => 'form-control']) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '科目名称',
                'id' => 'subject_id',
                'items' => $subjects
            ])
            {{--<div class="form-group">--}}
                {{--{!! Form::label('exam_id', '考试名称',['class' => 'col-sm-4 control-label']) !!}--}}
                {{--<div class="col-sm-3">--}}
                    {{--{!! Form::select('exam_id', $exams, null, ['class' => 'form-control']) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
               'label' => '考试名称',
               'id' => 'exam_id',
               'items' => $exams
           ])
            <div class="form-group">
                {!! Form::label('score', '分数',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('score', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过5个数字含小数点)',
                        'data-parsley-required' => 'true',
                        'data-parsley-type' => "number",
                        'data-parsley-maxlength' => '5',
                        ]) !!}
                </div>
            </div>
            @include('partials.enabled', ['enabled' => $score['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
