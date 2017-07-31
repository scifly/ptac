<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('user_id', '学生姓名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                {!! Form::select('user_id', $user, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('class_id', '班级名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                {!! Form::select('class_id', $class, null, ['class' => 'form-control']) !!}
            </div>
            </div>
            <div class="form-group">
                {!! Form::label('student_number', '学号',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('student_number', null, [
                    'class' => 'form-control',
                    'placeholder' => '小写字母与阿拉伯数字',
                     'data-parsley-type' => 'alphanum',
                    'data-parsley-required' => 'true',
                    'data-parsley-maxlength' => '32',
                    'data-parsley-minlength' => '2',

                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('card_number', '卡号',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('card_number', null, [
                    'class' => 'form-control',
                    'placeholder' => '小写字母与阿拉伯数字',
                    'data-parsley-required' => 'true',
                    'data-parsley-type' => 'alphanum',
                    'data-parsley-maxlength' => '32',
                    'data-parsley-minlength' => '2',

                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('birthday', '生日',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('birthday', null, [
                    'class' => 'form-control',
                    'placeholder' => '生日格式为2000-08-12形式',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('remark', null, [
                    'class' => 'form-control',
                    'placeholder' => '备注',
                    'data-parsley-required' => 'true',
                    'data-parsley-maxlength' => '32',
                    'data-parsley-minlength' => '2',


                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('oncampus', '是否住校',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::radio('oncampus', '1', true) !!}
                    {!! Form::label('oncampus', '是') !!}
                    {!! Form::radio('oncampus', '0') !!}
                    {!! Form::label('oncampus', '否') !!}
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
