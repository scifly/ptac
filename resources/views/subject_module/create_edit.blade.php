<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($subjectModules['id']))
                {!! Form::hidden('id', $subjectModules['id'], ['id' => 'id']) !!}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过20个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 20]'
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属科目',
                'id' => 'subject_id',
                'items' => $subjects
            ])
            <div class="form-group">
                {!! Form::label('weight', '次分类权重', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('weight', null, [
                        'class' => 'form-control',
                        'placeholder' => '次分类权重是数字',
                        'required' => 'true',
                        'type' => 'number',
                        'data-parsley-length' => '[1, 3]'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'enabled', 
                'value' => isset($subjectModules['enabled']) ? $subjectModules['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
