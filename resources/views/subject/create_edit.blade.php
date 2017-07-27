<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
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
                {!! Form::label('isaux', '是否为副科',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::radio('isaux', '1', true) !!}
                    {!! Form::label('isaux', '是') !!}
                    {!! Form::radio('isaux', '0') !!}
                    {!! Form::label('isaux', '否') !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('school_id', '所属学校',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('school_id', $school, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('grade_ids', '年级名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::select('grade_ids[]', $grades, null, ['class' => 'form-control', 'multiple' => 'multiple']) !!}
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
