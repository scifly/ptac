<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($sas['id']))
                {{ Form::hidden('id', $sas['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '不能超过60个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]',
                    ]) !!}
                </div>
            </div>
            @include('shared.single_select', [
                 'label' => '所属年级',
                 'id' => 'grade_id',
                 'items' => $grades,
                 'icon' => 'fa fa-object-group'
             ])
            @include('shared.single_select', [
                'label' => '所属学期',
                'id' => 'semester_id',
                'items' => $semesters,
            ])
            @include('shared.single_select', [
                'label' => '星期几',
                'id' => 'day',
                'items' => $days,
            ])

            <div class="form-group">
                {!! Form::label('start', '起始时间', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="bootstrap-timepicker">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-clock-o'])
                            {!! Form::text('start', null, [
                                'class' => 'form-control start-time timepicker',
                                'required' => 'true',
                                'data-parsley-start' => '.end-time',
                                'placeholder' => '(不得大于等于结束时间)'
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('end', '结束时间', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="bootstrap-timepicker">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-clock-o'])
                            {!! Form::text('end', null, [
                                'class' => 'form-control end-time timepicker',
                                'required' => 'true',
                                'data-parsley-end' => '.start-time',
                                'placeholder' => '(不得小于等于开始时间)'
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
            @include('shared.remark', [
                'field' => 'msg_template',
                'label' => '考勤消息模板',
                'placeholder' => '尊敬的{name}家长, 你的孩子于{time}在校打卡, 打卡规则：{rule}, 状态：{status}'
            ])
            @include('shared.switch', [
                'label' => '是否为公用',
                'id' => 'ispublic',
                'value' => $sas['ispublic'] ?? null,
                'options' => ['是', '否']                
            ])
            @include('shared.switch', [
              'label' => '进或出',
              'id' => 'inorout',
              'value' => $sas['inorout'] ?? null,
              'options' => ['进', '出']
          ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
