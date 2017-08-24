<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能超过20个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-maxlength' => '20',
                    'data-parsley-minlength' => '2',

                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('max_score', '最高分',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('max_score', null, [
                    'class' => 'form-control',
                    'placeholder' => '最高分必须是数字',
                    'data-parsley-required' => 'true',
                    'data-parsley-type' => 'integer',
                    'data-parsley-maxlength' => '3',
                    'data-parsley-minlength' => '3',

                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('pass_score', '及格分',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('pass_score', null, [
                    'class' => 'form-control',
                    'placeholder' => '及格分必须是数字',
                    'data-parsley-required' => 'true',
                    'data-parsley-type' => 'integer',
                    'data-parsley-maxlength' => '2',
                    'data-parsley-minlength' => '2',
                    ]) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('school_id', '所属学校',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('school_id', $schools, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('grade_ids', '年级名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {{--{!! Form::select('grade_ids[]', $grades, $abcs, ['class' => 'form-control', 'multiple' => 'multiple']) !!}--}}
                    <select multiple name="grade_ids[]" id="grade_ids"  data-parsley-required="true">
                        @foreach($grades as $key => $value)
                            @if(isset($selectedGrades))
                                <option value="{{$key}}" @if(array_key_exists($key, $selectedGrades)) selected @endif>
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
                {!! Form::label('isaux', '是否为副科', [
                    'class' => 'col-sm-4 control-label'
                ]) !!}
                <div class="col-sm-6" style="margin-top: 5px;">
                    <input id="isaux" type="checkbox" name="isaux" data-render="switchery"
                           data-theme="default" data-switchery="true"
                           @if(!empty($subject['enabled'])) checked @endif
                           data-classname="switchery switchery-small"/>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('enabled', '是否启用', [
                    'class' => 'col-sm-4 control-label'
                ]) !!}
                <div class="col-sm-6" style="margin-top: 5px;">
                    <input id="enabled" type="checkbox" name="enabled" data-render="switchery"
                           data-theme="default" data-switchery="true"
                           @if(!empty($subject['enabled'])) checked @endif
                           data-classname="switchery switchery-small"/>
                </div>
            </div>
        </div>
    </div>
    @include('partials.form_buttons')
</div>
