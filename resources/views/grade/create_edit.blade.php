<div class="box">
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
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 40]'
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'school_id',
                'items' => $schools
            ])
            @include('partials.multiple_select', [
                'label' => '年级主任',
                'id' => 'educator_ids',
                'items' => $educators,
                'selectedItems' => isset($selectedEducators) ? $selectedEducators : []
            ])
            @if (isset($grade['department_id']))
                {!! Form::hidden('department_id', $grade['department_id']) !!}
            @endif
            @include('partials.enabled', [
                'label' => '状态',
                'id' => 'enabled',
                'value' => isset($grade['enabled']) ? $grade['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>