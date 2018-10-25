<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
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
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-warning'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入警告类型名称)',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 60]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('english_name', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-language'])
                        {!! Form::text('english_name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入英文名称)',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 60]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $at['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
