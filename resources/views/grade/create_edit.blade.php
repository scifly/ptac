<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($grade) && !empty($grade['id']))
                {{ Form::hidden('id', $grade['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-object-group"></i>
                        </div>
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 40]'
                    ]) !!}
                    </div>
                </div>
            </div>
            @include('partials.multiple_select', [
                'label' => '年级主任',
                'id' => 'educator_ids',
                'items' => $educators,
                'selectedItems' => $selectedEducators ?? []
            ])
            @if (isset($grade['department_id']))
                {!! Form::hidden('department_id', $grade['department_id']) !!}
            @endif
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $grade['enabled'] ?? NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>