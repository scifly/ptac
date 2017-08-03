<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">

            <div class="form-group">
                {!! Form::label('custodian_id', '监护人姓名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('custodian_id', $cus_name, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('student_id', '学生姓名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('student_id', $student_name, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('relationship', '关系',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('relationship', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能少于2个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-minlength' => '2',

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
