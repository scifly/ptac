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
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>名</strong>
                        </div>
                        {!! Form::text('name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不超过40个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[4, 40]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '考试类型',
                'id' => 'exam_type_id',
                'items' => $examtypes
            ])
            @include('partials.multiple_select', [
                'label' => '参与班级',
                'id' => 'class_ids',
                'icon' => 'fa fa-users',
                'items' => $classes,
                'selectedItems' => isset($selectedClasses) ? $selectedClasses : []
            ])
            @include('partials.multiple_select', [
                'label' => '科目',
                'id' => 'subject_ids',
                'icon' => 'fa fa-book',
                'items' => $subjects,
                'selectedItems' => isset($selectedSubjects) ? $selectedSubjects : []
            ])
            <div class="form-group">
                {!! Form::label('daterange', '起止日期', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-calendar'])
                        {!! Form::text('daterange', isset($exam) ? $exam->start_date . ' ~ ' . $exam->end_date : null, [
                            'class' => 'form-control text-blue drange',
                            'placeholder' => '(开始日期 - 结束日期)',
                            'required' => 'true',
                        ]) !!}
                    </div>
                </div>
            </div>
            {{--<div class="form-group">--}}
                {{--{!! Form::label('start_date', '考试开始日期', [--}}
                    {{--'class' => 'col-sm-3 control-label'--}}
                {{--]) !!}--}}
                {{--<div class="col-sm-6">--}}
                    {{--{!! Form::date('start_date', null, [--}}
                        {{--'class' => 'form-control text-blue',--}}
                    {{--]) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--{!! Form::label('end_date', '考试结束日期', [--}}
                    {{--'class' => 'col-sm-3 control-label'--}}
                {{--]) !!}--}}
                {{--<div class="col-sm-6">--}}
                    {{--{!! Form::date('end_date', null, [--}}
                        {{--'class' => 'form-control text-blue',--}}
                    {{--]) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            @include('partials.remark')
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $exam['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
