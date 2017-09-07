<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
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
                {!! Form::label('remark', '备注', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过80个汉字)',
                        'max' => '255'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'enabled',
                'value' => isset($schoolType['enabled']) ? $schoolType['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
