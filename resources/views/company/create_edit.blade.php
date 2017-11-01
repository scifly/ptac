<div class="box">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($company['id']))
                {{ Form::hidden('id', $company['id'], ['id' => 'id']) }}
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
                {!! Form::label('remark', '备注', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('remark', null, [
                        'class' => 'form-control',
                        'required' => 'true'
                    ]) !!}
                </div>
            </div>
            @if (isset($company['department_id']))
                {!! Form::hidden('department_id', $company['department_id']) !!}
            @endif
            @if (isset($company['menu_id']))
                {!! Form::hidden('menu_id', $company['menu_id']) !!}
            @endif
            @include('partials.enabled', [
                'label' => '状态',
                'id' => 'enabled',
                'value' => isset($company['enabled']) ? $company['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
