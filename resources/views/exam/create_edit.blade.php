<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($exam['id']))
                {{ Form::hidden('id', $exam['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 40]'
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '所属考试类型',
                'id' => 'exam_type_id',
                'items' => $examtypes
            ])
            @include('partials.multiple_select', [
                'label' => '所属班级',
                'id' => 'class_ids',
                'items' => $classes,
                'selectedItems' => isset($selectedClasses) ? $selectedClasses : []
            ])
            @include('partials.multiple_select', [
                'label' => '科目',
                'id' => 'subject_ids',
                'items' => $subjects,
                'selectedItems' => isset($selectedSubjects) ? $selectedSubjects : []
            ])
            {{--<div class="form-group">--}}
                {{--{!! Form::label('max_scores', '科目满分', [--}}
                    {{--'class' => 'col-sm-3 control-label'--}}
                {{--]) !!}--}}
                {{--<div class="col-sm-6">--}}
                    {{--{!! Form::text('max_scores', null, [--}}
                        {{--'class' => 'form-control text-blue',--}}
                        {{--'placeholder' => '(不超过10个数字)',--}}
                        {{--'required' => 'true',--}}
                        {{--'type' => "number",--}}
                        {{--'data-parsley-range' => '[100,150]',--}}
                        {{--'data-parsley-length' => '[1, 10]'--}}
                    {{--]) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--{!! Form::label('pass_scores', '科目及格分数', [--}}
                    {{--'class' => 'col-sm-3 control-label'--}}
                {{--]) !!}--}}
                {{--<div class="col-sm-6">--}}
                    {{--{!! Form::text('pass_scores', null, [--}}
                        {{--'class' => 'form-control text-blue',--}}
                        {{--'placeholder' => '(不超过10个数字)',--}}
                        {{--'required' => 'true',--}}
                        {{--'type' => "number",--}}
                        {{--'data-parsley-range' => '[60,90]',--}}
                        {{--'data-parsley-length' => '[1, 10]'--}}
                    {{--]) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="form-group">
                {!! Form::label('start_date', '考试开始日期', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::date('start_date', null, [
                        'class' => 'form-control text-blue',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('end_date', '考试结束日期', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::date('end_date', null, [
                        'class' => 'form-control text-blue',
                    ]) !!}
                </div>
            </div>
            @include('partials.remark')
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $exam['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
