<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
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
                        @include('shared.icon_addon', ['class' => 'fa-object-group'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不超过40个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[4, 40]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.multiple_select', [
                'label' => '年级主任',
                'id' => 'educator_ids',
                'items' => $educators,
                'selectedItems' => $selectedEducators ?? []
            ])
            @if (isset($grade['department_id']))
                {!! Form::hidden('department_id', $grade['department_id']) !!}
            @endif
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $grade['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>