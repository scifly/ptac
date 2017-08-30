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
            @include('partials.single_select', [
               'label' => '所属学校',
               'id' => 'school_id',
               'items' => $schools
            ])
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
            @include('partials.enabled', ['enabled' => $attendance['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
