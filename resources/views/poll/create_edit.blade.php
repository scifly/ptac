<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($pq) && !empty($pq['id']))
                {{ Form::hidden('id', $pq['id']) }}
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
                        'data-parsley-length' => '[4, 40]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('start', '开始日期', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('start', null, ['class' => 'form-control datepicker']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('end', '结束日期', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('end', null, ['class' => 'form-control datepicker']) !!}
                </div>
            </div>
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $pq['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>