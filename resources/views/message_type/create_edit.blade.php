<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($mt['id']))
                {{ Form::hidden('id', $mt['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-lenght' => '[4, 40]'
                    ]) !!}
                </div>
            </div>
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $mt['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
