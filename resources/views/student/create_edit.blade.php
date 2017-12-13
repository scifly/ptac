<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 学生ID -->
            @if (!empty($student['id']))
                <!-- 学生ID -->
                {{ Form::hidden('id', $student['id'], ['id' => 'id']) }}
            @endif
            <!-- 学生UserID -->
            @if (!empty($student['user_id']))
                <!-- 学生UserID -->
                {{ Form::hidden('user_id', $student['user_id'], ['id' => 'user_id']) }}
            @endif
            <!-- 学生姓名 -->
            <div class="form-group">
                {{ Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-child"></i>
                        </div>
                        {{ Form::text('user[realname]', null, [
                            'class' => 'form-control',
                            'required' => 'true',
                            'placeholder' => '(请填写真实姓名)',
                            'data-parsley-length' => '[2, 30]'
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 英文名称 -->
            <div class="form-group">
                {{ Form::label('user[english_name]', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-language"></i>
                        </div>
                        {{ Form::text('user[english_name]', null, [
                            'class' => 'form-control',
                            'placeholder' => '请填写英文名(可选)',
                            'type' => 'string',
                            'data-parsley-length' => '[2, 255]'
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 性别 -->
            @include('partials.enabled', [
                'id' => 'user[gender]',
                'label' => '性别',
                'value' => isset($user['gender']) ? $user['gender'] : null,
                'options' => ['男', '女']
            ])
            <!-- 手机号码 -->
            @include('partials.mobile')
            <!-- 座机号码 -->
            <div class="form-group">
                {{ Form::label('user[telephone]', '座机', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-phone"></i>
                        </div>
                        {{ Form::text('user[telephone]', null, [
                            'class' => 'form-control',
                            'placeholder' => '请输入座机号码(可选}',
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 电子邮件 -->
            <div class="form-group">
                {{ Form::label('user[email]', '邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-envelope-o"></i>
                        </div>
                        {{ Form::text('user[email]', null, [
                            'class' => 'form-control',
                            'placeholder' => '(请输入电子邮件地址)',
                            'required' => 'true',
                            'type' => 'email',
                            'maxlength' => '255',
                            'data-parsley-type'=>"email"
                        ]) }}
                    </div>
                </div>
            </div>
            {{--<!-- 所属运营者 -->--}}
            {{--@if (isset($companies))--}}
                {{--@if(count($companies) > 1)--}}
                    {{--@include('partials.single_select', [--}}
                        {{--'label' => '所属运营者',--}}
                        {{--'id' => 'company_id',--}}
                        {{--'items' => $companies,--}}
                        {{--'icon' => 'fa fa-building'--}}
                    {{--])--}}
                {{--@else--}}
                    {{--<div class="form-group">--}}
                        {{--{{ Form::label('student[class_id]', '所属运营者', [--}}
                            {{--'class' => 'col-sm-3 control-label'--}}
                        {{--]) }}--}}
                        {{--<div class="col-sm-6" style="margin-top: 7px;">--}}
                            {{--<i class="fa fa-building"></i>&nbsp;{{ $companies[array_keys($companies)[0]] }}--}}
                            {{--{{ Form::hidden('company_id', array_keys($companies)[0], ['id' => 'company_id']) }}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--@endif--}}
            {{--@endif--}}
            {{--<!-- 所属企业 -->--}}
            {{--@if (isset($corps))--}}
                {{--@if(count($corps) > 1)--}}
                    {{--@include('partials.single_select', [--}}
                        {{--'label' => '所属企业',--}}
                        {{--'id' => 'corp_id',--}}
                        {{--'items' => $corps,--}}
                        {{--'icon' => 'fa fa-weixin'--}}
                    {{--])--}}
                {{--@else--}}
                    {{--<div class="form-group">--}}
                        {{--{{ Form::label('student[class_id]', '所属企业', [--}}
                            {{--'class' => 'col-sm-3 control-label'--}}
                        {{--]) }}--}}
                        {{--<div class="col-sm-6" style="margin-top: 7px;">--}}
                            {{--<i class="fa fa-weixin"></i>&nbsp;{{ $corps[array_keys($corps)[0]] }}--}}
                            {{--{{ Form::hidden('corp_id', array_keys($corps)[0], ['id' => 'corp_id']) }}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--@endif--}}
            {{--@endif--}}
            {{--<!-- 所属学校 -->--}}
            {{--@if (isset($schools))--}}
                {{--@if(count($schools) > 1)--}}
                    {{--@include('partials.single_select', [--}}
                        {{--'label' => '所属学校',--}}
                        {{--'id' => 'school_id',--}}
                        {{--'items' => $schools,--}}
                        {{--'icon' => 'fa fa-university'--}}
                    {{--])--}}
                {{--@else--}}
                    {{--<div class="form-group">--}}
                        {{--{{ Form::label('student[class_id]', '所属学校', [--}}
                            {{--'class' => 'col-sm-3 control-label'--}}
                        {{--]) }}--}}
                        {{--<div class="col-sm-6" style="margin-top: 7px;">--}}
                            {{--<i class="fa fa-university"></i>&nbsp;{{ $schools[array_keys($schools)[0]] }}--}}
                            {{--{{ Form::hidden('school_id', array_keys($schools)[0], ['id' => 'school_id']) }}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--@endif--}}
            {{--@endif--}}
            <!-- 所属年级 -->
            @if (isset($grades))
                @if(count($grades) > 1)
                    @include('partials.single_select', [
                        'label' => '所属年级',
                        'id' => 'grade_id',
                        'items' => $grades,
                        'icon' => 'fa fa-object-group'
                    ])
                @else
                    <div class="form-group">
                        {{ Form::label('class_id', '所属年级', [
                            'class' => 'col-sm-3 control-label'
                        ]) }}
                        <div class="col-sm-6" style="margin-top: 7px;">
                            <i class="fa fa-object-group"></i>&nbsp;{{ $grades[array_keys($grades)[0]] }}
                            {{ Form::hidden('grade_id', array_keys($grades)[0], ['id' => 'grade_id']) }}
                        </div>
                    </div>
                @endif
            @endif
            <!-- 所属班级 -->
{{--            @if(count($classes) > 1)--}}
            <div class="form-group">
                {!! Form::label('class_id', '所属班级', [
                    'class' => 'col-sm-3 control-label',
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-users"></i>
                        </div>
                        {!! Form::select('class_id', $classes, null, [
                            'class' => 'form-control select2',
                            'id' => 'classId',
                            'style' => 'width: 100%;'
                        ]) !!}
                    </div>
                </div>
            </div>
            {{--@else--}}
                {{--<div class="form-group">--}}
                    {{--{{ Form::label('student[class_id]', '所属班级', [--}}
                        {{--'class' => 'col-sm-3 control-label'--}}
                    {{--]) }}--}}
                    {{--<div class="col-sm-6" style="margin-top: 7px;">--}}
                        {{--<i class="fa fa-users"></i>&nbsp;{{ $classes[array_keys($classes)[0]] }}--}}
                        {{--{{ Form::hidden('student[class_id]', array_keys($classes)[0], ['id' => 'class_id']) }}--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--@endif--}}
            <!-- 学号 -->
            <div class="form-group">
                {!! Form::label('student_number', '学号', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('student_number', null, [
                        'class' => 'form-control',
                        'placeholder' => '小写字母与阿拉伯数字',
                        'data-parsley-type' => 'alphanum',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 32]'
                    ]) !!}
                </div>
            </div>
            <!-- 卡号 -->
            <div class="form-group">
                {!! Form::label('card_number', '卡号', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('card_number', null, [
                        'class' => 'form-control',
                        'placeholder' => '小写字母与阿拉伯数字',
                        'required' => 'true',
                        'data-parsley-type' => 'alphanum',
                        'data-parsley-length' => '[2, 32]'
                    ]) !!}
                </div>
            </div>
            <!-- 生日 -->
            <div class="form-group">
                {!! Form::label('birthday', '生日', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('birthday', null, [
                        'required' => 'true',
                        'class' => 'form-control',
                        'placeholder' => '生日格式为2000-08-12形式',
                    ]) !!}

                </div>
            </div>
            <!-- 是否住校 -->
            @include('partials.enabled', [
                'label' => '是否住校',
                'id' => 'oncampus',
                'value' => isset($student['oncampus']) ? $student['oncampus'] : NULL,
                'options' => ['住校', '走读']
            ])
            <!-- 备注 -->
            @include('partials.remark', [
                'field' => 'user[remark]'
            ])

            <!-- 状态 -->
            @include('partials.enabled', [
                'id' => 'user[enabled]',
                'value' => isset($student['enabled']) ? $student['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
