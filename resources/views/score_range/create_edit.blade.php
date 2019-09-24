<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($sr))
                {!! Form::hidden('id', $sr['id']) !!}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '请输入成绩项名称',
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
                        'class' => 'form-control text-blue',
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
                        'class' => 'form-control text-blue',
                        'placeholder' => '最多两位小数',
                        'required' => 'true',
                        'type' => 'number',
                        'pattern' => '/^\d+(\.\d{1,2})?$/',
                    ]) !!}
                </div>
            </div>
            @include('shared.multiple_select', [
                'label' => '统计科目',
                'id' => 'subject_ids',
                'items' => $subjects,
                'selectedItems' => $selectedSubjects
            ])
            @include('shared.switch', [
                'id' => 'enabled',
                'value' => $sr['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>