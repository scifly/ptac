<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
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
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 40]'
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属年级',
                'id' => 'grade_id',
                'items' => $grades
            ])
            @include('partials.multiple_select', [
                'label' => '班级主任',
                'id' => 'educator_ids',
                'items' => $educators,
                'selectedItems' => isset($selectedEducators) ? $selectedEducators : NULL
            ])
            @if (isset($class['department_id']))
                {!! Form::hidden('department_id', $class['department_id']) !!}
            @endif
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'enabled',
                'value' => isset($class['enabled']) ? $class['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>