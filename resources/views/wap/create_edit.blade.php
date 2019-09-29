<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            {!! Form::hidden('id', $wap['id']) !!}
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '首页Title'])
                <div class="col-sm-3">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '不能超过40个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 40]'
                    ]) !!}
                </div>
            </div>
            @include('shared.wap.preview')
            @include('shared.switch', ['value' => $wap['enabled'] ?? null])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
@include('shared.wap.modal_uploader')
