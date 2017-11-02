<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($comboType['id']))
                {{ Form::hidden('id', $comboType['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '套餐名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入套餐名称)',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('amount', '金额', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('amount', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入套餐金额)',
                        'required' => 'true',
                        'type' => 'number',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('discount', '折扣', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('discount', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入套餐折扣)',
                        'required' => 'true',
                        'type' => 'number',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('months', '有效月数', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('months', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入有效月数)',
                        'required' => 'true',
                        'type' => 'number',
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'school_id',
                'items' => $schools,
            ])
            @include('partials.enabled', [
                'label' => '状态',
                'id' => 'enabled',
                'value' => isset($comboType['enabled']) ? $comboType['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>