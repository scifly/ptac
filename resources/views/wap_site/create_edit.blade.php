<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            {{ Form::hidden('id', $ws['id']) }}
            <div class="form-group">
                {!! Form::label('site_title', '首页抬头', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-3">
                    {!! Form::text('site_title', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '不能超过40个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 40]'
                    ]) !!}
                </div>
            </div>
            @include('shared.wapsite.preview')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $ws['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
@include('shared.wapsite.modal_uploader')
