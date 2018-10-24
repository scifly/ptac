<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($op['id']))
                {{ Form::hidden('id', $op['id'], [ 'id' => 'id' ]) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-building text-blue'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不超过40个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[4, 40]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @if (isset($op['department_id']))
                {!! Form::hidden('department_id', $op['department_id']) !!}
            @endif
            @if (isset($op['menu_id']))
                {!! Form::hidden('menu_id', $op['menu_id']) !!}
            @endif
            @include('partials.remark')
            @include('partials.switch', [
                'id' => 'enabled',
                'value' => $op['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
