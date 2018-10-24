<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($ct['id']))
                {{ Form::hidden('id', $ct['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-commenting'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入通信方式名称)',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 60]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('partials.remark')
            @include('partials.switch', [
                'id' => 'enabled',
                'value' => $ct['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>