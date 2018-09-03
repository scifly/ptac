<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
        @if (!empty($tag['id']))
                {{ Form::hidden('id', $tag['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label']
                ) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', [
                            'class' => 'fa-tag'
                        ])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不得超过32个汉字)',
                            'required' => 'true',
                            'maxlength' => '32'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('partials.remark')
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $tag['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
