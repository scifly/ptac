<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过20个汉字)',
                        'data-parsley-required' => 'true',
                        'maxlength' => '60'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('location', '安装位置',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('location', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过80个汉字)',
                        'data-parsley-required' => 'true',
                        'maxlength' => '255'
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
                {!! Form::label('machineid', '考勤机id',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('machineid', null, [
                        'class' => 'form-control',
                        'placeholder' => '(小写字母和数字，不超过20个字符)',
                        'data-parsley-required' => 'true',
                        'data-parsley-type' => 'alphanum',
                        'maxlength' => '20'
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
    @include('partials.form.buttons')
</div>
