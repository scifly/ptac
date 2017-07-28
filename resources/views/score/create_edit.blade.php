<div class="box box-primary">
    <div class="box-header"></div>
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
                {!! Form::label('class_rank', '班级排名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('class_rank', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过5个数字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-type' => "number",
                        'data-parsley-maxlength' => '5'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('grade_rank', '年级排名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('grade_rank', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过5个数字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-type' => "number",
                        'data-parsley-maxlength' => '5'
                        ]) !!}
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
                        'data-parsley-maxlength' => '5'
                        ]) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-4">
                    {!! Form::radio('enabled', '1', true) !!}
                    {!! Form::label('enabled', '启用') !!}
                    {!! Form::radio('enabled', '0') !!}
                    {!! Form::label('enabled', '禁用') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer">
        {{--button--}}
        <div class="form-group">
            <div class="col-sm-3 col-sm-offset-4">
                {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'save']) !!}
                {!! Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}
            </div>
        </div>
    </div>
</div>
