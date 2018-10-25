<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($class['id']))
                {{ Form::hidden('id', $class['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-users'])
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不超过40个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[4, 40]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.single_select', [
                'label' => '所属年级',
                'id' => 'grade_id',
                'items' => $grades,
                'icon' => 'fa fa-object-group'
            ])
            @include('shared.multiple_select', [
                'label' => '班级主任',
                'id' => 'educator_ids',
                'items' => $educators,
                'selectedItems' => $selectedEducators ?? null
            ])
            @if (isset($class['department_id']))
                {!! Form::hidden('department_id', $class['department_id']) !!}
            @endif
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $class['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>