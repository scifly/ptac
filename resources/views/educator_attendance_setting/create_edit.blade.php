<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($eas['id']))
                {{ Form::hidden('id', $eas['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '不能超过60个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]'
                    ]) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('start', '起始时间', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="bootstrap-timepicker">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-clock-o'])
                            {!! Form::text('start', null, [
                                'class' => 'form-control start-time timepicker',
                                'required' => 'true',
                                'data-parsley-start' => '.end-time',
                                'placeholder' => '(不得大于等于结束时间)'
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('end', '结束时间', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="bootstrap-timepicker">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-clock-o'])
                            {!! Form::text('end', null, [
                                'class' => 'form-control end-time timepicker',
                                'required' => 'true',
                                'data-parsley-end' => '.start-time',
                                'placeholder' => '(不得小于等于开始时间)'
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
            @include('shared.switch', [
                'label' => '进或出',
                'id' => 'inorout',
                'value' => $eas['inorout'] ?? null,
                'options' => ['进', '出']
            ])
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $eas['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
