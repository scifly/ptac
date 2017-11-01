<div class="box">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($alertType['id']))
                {{ Form::hidden('id', $alertType['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '警告类型名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入警告类型名称)',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('english_name', '英文名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('english_name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入英文名称)',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '状态',
                'id' => 'enabled',
                'value' => isset($alertType['enabled']) ? $alertType['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>