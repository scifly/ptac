<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($ico))
                {!! Form::hidden('id', $ico['id'], ['id' => 'id']) !!}
            @endif
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入图标CSS类名称)',
                        'required' => 'true',
                        'maxlength' => '80'
                    ]) !!}
                </div>
            </div>
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $ico['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>