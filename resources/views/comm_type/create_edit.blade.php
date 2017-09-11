<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($commType['id']))
                {{ Form::hidden('id', $commType['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '通信方式名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入功能名称)',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]'
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
                        'placeholder' => '(请输入方法名称)',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 255]',
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'enabled',
                'value' => isset($commType['enabled']) ? $commType['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>