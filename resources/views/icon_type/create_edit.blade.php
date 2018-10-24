<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($it['id']))
                {!! Form::hidden('id', $it['id'], ['id' => 'id']) !!}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称',[
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(请输入功能名称)',
                        'required' => 'true',
                        'maxlength' => '80'
                    ]) !!}

                </div>
            </div>
            @include('partials.remark')
            @include('partials.switch', [
                'id' => 'enabled',
                'value' => $it['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
