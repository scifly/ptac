<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('student_id', '学号',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::select('student_id', $students, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('subject_id', '科目名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::select('subject_id', $subjects, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('exam_id', '考试名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::select('exam_id', $exams, null, ['class' => 'form-control']) !!}
                </div>
            </div>
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
            {{--<div class="form-group">--}}
                {{--{!! Form::label('enabled', '是否启用', [--}}
                    {{--'class' => 'col-sm-4 control-label'--}}
                {{--]) !!}--}}
                {{--<div class="col-sm-6" style="margin-top: 5px;">--}}
                    {{--<input id="enabled" type="checkbox" name="enabled" data-render="switchery"--}}
                           {{--data-theme="default" data-switchery="true"--}}
                           {{--@if(!empty($score['enabled'])) checked @endif--}}
                           {{--data-classname="switchery switchery-small"/>--}}
                {{--</div>--}}
            {{--</div>--}}
            @include('partials.enabled', ['enabled' => $score['enabled']])
        </div>
    </div>
    {{--<div class="box-footer">--}}
        {{--button--}}
        {{--<div class="form-group">--}}
            {{--<div class="col-sm-3 col-sm-offset-4">--}}
                {{--{!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'save']) !!}--}}
                {{--{!! Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    @include('partials.form_buttons')
</div>
