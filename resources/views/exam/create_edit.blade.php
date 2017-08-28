<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('id', 'id',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::hidden('id', null, [
                        'class' => 'form-control',
                    ]) !!}
                </div>

            </div>
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
                <div class="col-sm-2">
                    <select multiple="multiple" name="class_ids[]" id="class_ids">
                        @foreach($classes as $key => $value)
                            @if(isset($selectedClasses))
                                <option value="{{$key}}" @if(array_key_exists($key,$selectedClasses))selected="selected"@endif>
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
                <div class="col-sm-2">
                    <select multiple="multiple" name="subject_ids[]" id="subject_ids">
                        @foreach($subjects as $key => $value)
                            @if(isset($selectedSubjects))
                                <option value="{{$key}}" @if(array_key_exists($key,$selectedSubjects))selected="selected"@endif>
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
            {{--<div class="form-group">--}}
                {{--{!! Form::label('enabled', '是否启用', [--}}
                    {{--'class' => 'col-sm-4 control-label'--}}
                {{--]) !!}--}}
                {{--<div class="col-sm-6" style="margin-top: 5px;">--}}
                    {{--<input id="enabled" type="checkbox" name="enabled" data-render="switchery"--}}
                           {{--data-theme="default" data-switchery="true"--}}
                           {{--@if(!empty($exam['enabled'])) checked @endif--}}
                           {{--data-classname="switchery switchery-small"/>--}}
                {{--</div>--}}
            {{--</div>--}}
            @include('partials.enabled', ['enabled' => $exam['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
