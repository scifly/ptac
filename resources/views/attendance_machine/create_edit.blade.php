<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($am['id']))
                {{ Form::hidden('id', $am['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-print'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不得超过20个汉字)',
                            'required' => 'true',
                            'maxlength' => '60'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('location', '安装位置', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-location-arrow'])
                        {!! Form::text('location', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不得超过80个汉字)',
                            'required' => 'true',
                            'maxlength' => '255'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('machineid', '考勤机id', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('machineid', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(小写字母和数字，不超过20个字符)',
                        'required' => 'true',
                        'maxlength' => '20'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $am['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>