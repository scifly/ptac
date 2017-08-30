<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('name', '名称', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过20个汉字)',
                        'data-parsley-required' => 'true',
                        'max' => '60'
                    ]) !!}
                </div>
            </div>
            {{--<div class="form-group">--}}
                {{--{!! Form::label('school_id', '所属学校',['class' => 'col-sm-4 control-label']) !!}--}}
                {{--<div class="col-sm-2">--}}
                    {{--{!! Form::select('school_type_id', $schools, null, ['class' => 'form-control']) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'school_type_id',
                'items' => $schools
            ])
            <div class="form-group">
                {!! Form::label('remark', '备注', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过80个汉字)',
                        'max' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('start_date', '起始日期', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        {!! Form::text('start_date', null, [
                            'class' => 'form-control pull-right',
                            'placeholder' => '(请选择起始日期)',
                            'data-parsley-required' => 'true',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('end_date', '结束日期', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        {!! Form::text('start_date', null, [
                            'class' => 'form-control pull-right',
                            'placeholder' => '(请选择结束日期)',
                            'data-parsley-required' => 'true',
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('partials.enabled', ['enabled' => $semester['enabled']])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
