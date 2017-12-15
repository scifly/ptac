<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($semester['id']))
                {!! Form::hidden('id', $semester['id'], ['id' => 'id']) !!}
            @endif
                {{ Form::hidden('school_id', $schoolId, ['id' => 'school_id']) }}
                <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过20个汉字)',
                        'required' => 'true',
                        'max' => '60'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('start_date', '起始日期', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        {!! Form::text('start_date', null, [
                            'class' => 'form-control pull-right',
                            'placeholder' => '(请选择起始日期)',
                            'required' => 'true',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('end_date', '结束日期', [
                    'class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        {!! Form::text('start_date', null, [
                            'class' => 'form-control pull-right',
                            'placeholder' => '(请选择结束日期)',
                            'required' => 'true',
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('partials.remark')
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => isset($semester['enabled']) ? $semester['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
