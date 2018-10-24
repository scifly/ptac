<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($st['id']))
                {!! Form::hidden('id', $st['id'], ['id' => 'id']) !!}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(不得超过20个汉字)',
                        'required' => 'true',
                        'maxlength' => '20'
                    ]) !!}
                </div>
            </div>
            @include('partials.remark')
            @include('partials.switch', [
                'id' => 'enabled',
                'value' => $st['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
