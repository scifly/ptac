<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($pollQuestionnaire) && !empty($pollQuestionnaire['id']))
                {{ Form::hidden('id', $pollQuestionnaire['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
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
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $pollQuestionnaire['enabled'] ?? NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>