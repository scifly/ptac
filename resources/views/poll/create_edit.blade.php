<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($poll))
                {!! Form::hidden('id', $poll['id']) !!}
            @endif
            <div class="form-group">
                @include('shared.label', ['field' => 'name', 'label' => '名称'])
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 40]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                @include('shared.label', ['field' => 'start', 'label' => '开始日期'])
                <div class="col-sm-6">
                    {!! Form::text('start', null, ['class' => 'form-control datepicker']) !!}
                </div>
            </div>
            <div class="form-group">
                @include('shared.label', ['field' => 'end', 'label' => '结束日期'])
                <div class="col-sm-6">
                    {!! Form::text('end', null, ['class' => 'form-control datepicker']) !!}
                </div>
            </div>
            @include('shared.switch', ['value' => $poll['enabled'] ?? null])
        </div>
    </div>
    @include('shared.form_buttons')
</div>