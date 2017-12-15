<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($messageType['id']))
                {{ Form::hidden('id', $messageType['id'], ['id' => 'id']) }}
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
                        'data-parsley-lenght' => '[4, 40]'
                    ]) !!}
                </div>
            </div>
            @include('partials.remark')
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $messageType['enabled'] ?? NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
