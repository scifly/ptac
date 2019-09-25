<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($op))
                {!! Form::hidden('id', $op['id']) !!}
                {!! Form::hidden('department_id', $op['department_id']) !!}
                {!! Form::hidden('menu_id', $op['menu_id']) !!}
            @endif
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-building text-blue'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不超过40个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[4, 40]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.remark')
            @include('shared.switch', [

                'value' => $op['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
