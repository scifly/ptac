<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($iconType['id']))
                {!! Form::hidden('id', $iconType['id'], ['id' => 'id']) !!}
            @endif
            <div class="form-group">
                {!! Form::label('name', 'Icon类型名称',[
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入功能名称)',
                        'required' => 'true',
                        'maxlength' => '80'
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
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '状态',
                'id' => 'enabled',
                'value' => isset($iconType['enabled']) ? $iconType['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
