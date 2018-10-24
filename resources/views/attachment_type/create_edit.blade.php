<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($at['id']))
                {{ Form::hidden('id', $at['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入附件类型名称)',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]'
                    ]) !!}
                </div>
            </div>
            @include('partials.remark')
            @include('partials.switch', [
                'id' => 'enabled',
                'value' => $at['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>