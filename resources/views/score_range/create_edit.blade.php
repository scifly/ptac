<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($scoreRange['id']))
                {{ Form::hidden('id', $scoreRange['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '成绩项名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '请输入成绩项名称(例：200-400)',
                        'required' => 'true',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('start_score', '起始分数', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('start_score', null, [
                        'class' => 'form-control',
                        'placeholder' => '最多两位小数',
                        'required' => 'true',
                        'type' => 'number',
                        'pattern' => '/^\d+(\.\d{1,2})?$/',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('end_score', '截止分数', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('end_score', null, [
                        'class' => 'form-control',
                        'placeholder' => '最多两位小数',
                        'required' => 'true',
                        'type' => 'number',
                        'pattern' => '/^\d+(\.\d{1,2})?$/',
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'school_id',
                'items' => $schools
            ])
            @include('partials.multiple_select', [
                'label' => '统计科目',
                'id' => 'subject_ids',
                'items' => $subjects
            ])
            {!! Form::hidden('subject_select_ids', $scoreRange['subject_ids'] or '') !!}
            @include('partials.enabled', [
                'label' => '状态',
                'id' => 'enabled',
                'value' => isset($subject['enabled']) ? $subject['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>