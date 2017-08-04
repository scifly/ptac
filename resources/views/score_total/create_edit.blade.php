<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('student_id', '学号',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('student_id', $students, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('exam_id', '考试名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('exam_id', $exams, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('score', '总成绩',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('score', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过5个数字含小数点)',
                        'data-parsley-required' => 'true',
                        'data-parsley-type' => "number",
                        'data-parsley-maxlength' => '5',
                        ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('subject_ids', '计入总成绩科目名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    <input type="hidden" id="subject_select_ids" value="{{ $scoreTotal['subject_ids'] or '' }}">
                    <select name="subject_ids[]" id="subject_ids" multiple="multiple" class="form-control"  >
                    </select>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('na_subject_ids', '未计入总成绩科目名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    <input type="hidden" id="na_subject_select_ids" value="{{ $scoreTotal['na_subject_ids'] or '' }}">
                    <select multiple="multiple" class="form-control" name="na_subject_ids[]" id="na_subject_ids" disabled="disabled">
                    </select>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('class_rank', '班级排名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
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
                <div class="col-sm-2">
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
