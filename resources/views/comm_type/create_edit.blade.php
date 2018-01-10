<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($commType['id']))
                {{ Form::hidden('id', $commType['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-commenting"></i>
                        </span>
                        {!! Form::text('name', null, [
                            'class' => 'form-control',
                            'placeholder' => '(请输入通信方式名称)',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 60]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('partials.remark')
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $commType['enabled'] ?? NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>