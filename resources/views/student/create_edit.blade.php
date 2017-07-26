<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('student_number', '学号',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('student_number', null, [
                    'class' => 'form-control',
                    'placeholder' => '学号必须是数字',
                    'data-parsley-required' => 'true',

                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('card_number', '卡号',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('card_number', null, [
                    'class' => 'form-control',
                    'placeholder' => '卡号必须是数字',
                    'data-parsley-required' => 'true',
                     'data-parsley-type' => 'integer',

                    ]) !!}
                </div>
            </div>
      

            <div class="form-group">
                {!! Form::label('isaux', '是否为副科',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::radio('isaux', '1', true) !!}
                    {!! Form::label('isaux', '是') !!}
                    {!! Form::radio('isaux', '0') !!}
                    {!! Form::label('isaux', '否') !!}
                </div>
            </div>
            {{--<div class="form-group">--}}
                {{--{!! Form::label('school_id', '所属学校',['class' => 'col-sm-4 control-label']) !!}--}}
                {{--<div class="col-sm-2">--}}
                    {{--{!! Form::select('school_id', $school, null, ['class' => 'form-control']) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--{!! Form::label('grade_ids', '年级名称',['class' => 'col-sm-4 control-label']) !!}--}}
                {{--<div class="col-sm-3">--}}
                    {{--{!! Form::select('grade_ids[]', $grades, null, ['class' => 'form-control', 'multiple' => 'multiple']) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
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
