<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($studentAttendanceSetting['id']))
                {{ Form::hidden('id', null, ['id' => 'id', 'value' => $studentAttendanceSetting['id']]) }}
            @endif
                <div class="form-group">
                    {!! Form::label('name', '名称',['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('name', null, [
                            'class' => 'form-control',
                            'placeholder' => '不能超过20个汉字',
                            'data-parsley-required' => 'true',
                            'data-parsley-maxlength' => '20',
                            'data-parsley-minlength' => '2',
                        ]) !!}
                    </div>
                </div>
                @include('partials.single_select', [
                    'label' => '所属年级',
                    'id' => 'grade_id',
                    'items' => $grades
                ])
                @include('partials.single_select', [
                    'label' => '学期',
                    'id' => 'semester_id',
                    'items' => $semesters,
                ])
                <div class="form-group">
                    {!! Form::label('start', '起始时间',['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('start', null, ['class' => 'form-control start-date',]) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('end', '结束时间',['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('end', null, ['class' => 'form-control end-date',]) !!}
                    </div>
                </div>
                @include('partials.single_select', [
                    'label' => '星期几',
                    'id' => 'day',
                    'items' => $days
                ])
                <div class="form-group">
                    {!! Form::label('msg_template', '考勤消息模板',['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('msg_template', null, [
                            'class' => 'form-control',
                            'placeholder' => '不能超过20个汉字',
                            'data-parsley-required' => 'true',
                            'data-parsley-maxlength' => '20',
                            'data-parsley-minlength' => '2',
                        ]) !!}
                    </div>
                </div>
            @include('partials.enabled', [
           'label' => '是否公开',
           'for' => 'ispublic',
           'value' => isset($studentAttendanceSetting['ispublic'])?$studentAttendanceSetting['ispublic']:''])
            @include('partials.enabled', [
            'label' => '进或出',
            'for' => 'inorout',
            'value' => isset($studentAttendanceSetting['inorout'])?$studentAttendanceSetting['inorout']:''])
            @include('partials.enabled', [
            'label' => '是否启用',
            'for' => 'enabled',
            'value' => isset($studentAttendanceSetting['enabled'])?$studentAttendanceSetting['enabled']:''])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
