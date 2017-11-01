<div class="box">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($cr['id']))
                {{ Form::hidden('id', $cr['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 40]'
                    ]) !!}
                </div>
            </div>
            {!! Form::hidden('school_id', $schoolId, ['id' => 'school_id']) !!}
            <div class="form-group">
                {!! Form::label('capacity', '容量', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('capacity', null, [
                        'class' => 'form-control',
                        'placeholder' => '(会议室可容纳的人数)',
                        'required' => 'true',
                        'type' => 'number'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('remark', '备注', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入备注)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 255]'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '状态',
                'id' => 'enabled',
                'value' => isset($cr['enabled']) ? $cr['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>