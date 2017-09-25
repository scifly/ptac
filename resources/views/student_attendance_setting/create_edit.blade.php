<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($studentAttendanceSetting['id']))
                {{ Form::hidden('id', $studentAttendanceSetting['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过60个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]',
                    ]) !!}
                </div>
            </div>
            @include('partials.single_select', [
             'label' => '所属年级',
             'id' => 'grade_id',
             'items' => $grades
         ])
            @include('partials.single_select', [
                'label' => '所属学期',
                'id' => 'semester_id',
                'items' => $semesters,
            ])
            @include('partials.single_select', [
            'label' => '星期几',
            'id' => 'day',
            'items' => $days,
        ])
            <div class="form-group">
                {!! Form::label('start', '考勤时段起始时间', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('start', null, [
                        'class' => 'form-control start-date',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('end', '考勤时段结束时间', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('end', null, [
                        'class' => 'form-control end-date',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('msg_template', '考勤消息模板', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('msg_template', null, [
                        'class' => 'form-control',
                        'placeholder' => '消息模板必须为字符',
                        'required' => 'true',
                        'type' => 'integer',
                        'data-parsley-length' => '[2, 255]'
                    ]) !!}
                </div>
            </div>

            @include('partials.enabled', [
                'label' => '是否为公用',
                'id' => 'ispublic',
                'value' => isset($studentAttendanceSetting['ispublic']) ? $studentAttendanceSetting['ispublic']: NULL
            ])
            @include('partials.enabled', [
              'label' => '进或出',
              'id' => 'inorout',
              'value' => isset($studentAttendanceSetting['ispublic']) ? $studentAttendanceSetting['ispublic']: NULL
          ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
