<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($cr['id']))
                {{ Form::hidden('id', $cr['id'], ['id' => 'id']) }}
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
                {!! Form::label('capacity', '容量', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('capacity', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(会议室可容纳的人数)',
                        'required' => 'true',
                        'type' => 'number'
                    ]) !!}
                </div>
            </div>
            @include('shared.remark')
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $cr['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>