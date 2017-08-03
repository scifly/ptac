<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">
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
            <div class="form-group">
                {!! Form::label('exam_type_id', '所属考试类型',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('exam_type_id', $examtypes, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('class_ids', '班级',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    <select multiple="multiple" name="class_ids[]" id="class_ids">
                        @foreach($classes as $key => $value)
                            @if(isset($classIds))
                                <option value="{{$key}}" @if(array_key_exists($key,$classIds))selected="selected"@endif>
                                    {{$value}}
                                </option>
                            @else
                                <option value="{{$key}}">{{$value}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('subject_ids', '科目',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-5">
                    <select multiple="multiple" name="subject_ids[]" id="subject_ids">
                        @foreach($subjects as $key => $value)
                            @if(isset($subjectIds))
                                <option value="{{$key}}" @if(array_key_exists($key,$subjectIds))selected="selected"@endif>
                                    {{$value}}
                                </option>
                            @else
                                <option value="{{$key}}">{{$value}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
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
