<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($indicator))
                {!! Form::hidden('id', $indicator['id']) !!}
            @endif
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'required' => 'true',
                        'maxlength' => '80'
                    ]) !!}
                </div>
            </div>
            @include('shared.switch', [
                'id' => 'sign',
                'value' => $indicator['sign'] ?? null,
                'options' => ['加分项', '减分项']
            ])
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $indicator['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>