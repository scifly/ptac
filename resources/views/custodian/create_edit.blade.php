<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 监护人ID -->
        @if (!empty($custodian['id']))
            {{ Form::hidden('id', $custodian['id'], ['id' => 'id']) }}
        @endif
        <!-- 监护人UserID -->
        @if (!empty($custodian['user_id']))
            {{ Form::hidden('user_id', $custodian['user_id'], ['id' => 'user_id']) }}
        @endif
        <!-- 监护人姓名 -->
            <div class="form-group">
                {{ Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </div>
                        {{ Form::text('user[realname]', null, [
                            'class' => 'form-control',
                            'required' => 'true',
                            'placeholder' => '(请填写真实姓名)',
                            'data-parsley-length' => '[2, 255]'
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 监护人英文名 -->
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
            <!-- 监护人性别 -->
        @include('partials.enabled', [
            'label' => '性别',
            'id' => 'user[gender]',
            'value' => isset($custodian->user->gender) ? $custodian->user->gender : NULL,
            'options' => ['男', '女']
        ])
        <!-- 监护人手机列表 -->
        @include('partials.mobile')
        <!-- 监护人座机号码 -->
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
            <!-- 监护人电子邮件地址 -->
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
                            'maxlength' => '255',
                            'data-parsley-type'=>"email"
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 被监护人列表 -->
            <div class="form-group">
                <label class="col-sm-3 control-label">被监护人</label>
                <div class="col-sm-6" style="padding-top: 3px;">
                    {{--@if(!isset($pupils))--}}
                    {{--<button id="add-pupil" class="btn btn-box-tool" type="button">--}}
                    {{--<i class="fa fa-user-plus text-blue"> 添加</i>--}}
                    {{--</button>--}}
                    {{--@else--}}
                    {{--<div id="department-nodes-checked">--}}
                    {{--<button type="button" class="btn btn-flat" style="margin-right: 5px;margin-bottom: 5px">--}}
                    {{--<i class=""></i>--}}
                    {{--张三--}}
                    {{--<i class="fa fa-close close-selected"></i>--}}
                    {{--<input type="hidden" name="selectedStudents[]" value="1"/>--}}
                    {{--</button>--}}
                    {{--</div>--}}
                    <table class="table table-striped table-bordered table-hover table-condensed">
                        <thead>
			<tr class="bg-info">
                            <th>学生</th>
                            <th>学号</th>
                            <th>监护人关系</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id="tBody">
                        @if(!empty($pupils))
                            @foreach($pupils as $key => $pupil)
                                <tr>
                                    <input type="hidden" value="{{$pupil->student_id}}" name="student_ids[{{$key}}]" id="student_ids">
                                    <td>{{$pupil->student->user->realname}}</td>
                                    <td>{{$pupil->student->student_number}}</td>
                                    <td>
                                        <input type="text" name="relationships[{{$key}}]" id="" readonly class="no-border" style="background: none" value="{{$pupil->relationship}}">
                                    </td>
                                    <td>
                                        <a href="javascript:" class="delete">
                                            <i class="fa fa-trash-o text-blue"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        </tbody>
                    </table>
                    <button id="add-pupil" class="btn btn-box-tool" type="button">
                        <i class="fa fa-user-plus text-blue"></i>
                    </button>
                    {{--@endif--}}
                </div>
            </div>
            <!-- 监护人角色 -->
        {!! Form::hidden('user[group_id]', $groupId) !!}
        <!-- 监护人状态 -->
            @include('partials.enabled', [
                'id' => 'user[enabled]',
                'value' => isset($custodian->user->enabled) ? $custodian->user->enabled : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
<!-- 添加被监护人 -->
<div class="modal fade" id="pupils">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">被监护人</h4>
            </div>
            <div class="modal-body with-border">
                <div class="form-horizontal">
                    <!-- 所属学校 -->
                    <div class="form-group">
                        @if(isset($schools))
                            @include('partials.single_select', [
                                    'id' => 'schoolId',
                                    'label' => '所属学校',
                                    'items' => $schools
                                ])
                            {{--@if($schools->count() > 1)--}}
                            {{--@include('partials.single_select', [--}}
                            {{--'id' => 'schoolId',--}}
                            {{--'label' => '所属学校',--}}
                            {{--'items' => $schools--}}
                            {{--])--}}
                            {{--@else--}}
                            {{--{{ Form::label('schoolId', '所属学校', [--}}
                            {{--'class' => 'control-label col-sm-3'--}}
                            {{--]) }}--}}
                            {{--<div class="col-sm-6" style="padding-top: 8px;">--}}
                            {{--{{ $grades->first() }}--}}
                            {{--{{ Form::hidden('schoolId', $grades->keys()->first(), [--}}
                            {{--'id' => 'schoolId'--}}
                            {{--]) }}--}}
                            {{--</div>--}}
                            {{--@endif--}}
                        @endif
                    </div>
                    <!-- 所属年级 -->
                    <div class="form-group">
                        @if(isset($grades))
                            @include('partials.single_select', [
                                    'id' => 'gradeId',
                                    'label' => '所属年级',
                                    'items' => $grades
                                ])
                            {{--@if($grades->count() > 1)--}}
                            {{--@include('partials.single_select', [--}}
                            {{--'id' => 'gradeId',--}}
                            {{--'label' => '所属年级',--}}
                            {{--'items' => $grades--}}
                            {{--])--}}
                            {{--@else--}}
                            {{--{{ Form::label('gradeId', '所属班级', [--}}
                            {{--'class' => 'control-label col-sm-3'--}}
                            {{--]) }}--}}
                            {{--<div class="col-sm-6" style="padding-top: 8px;">--}}
                            {{--{{ $grades->first() }}--}}
                            {{--{{ Form::hidden('gradeId', $grades->keys()->first(), [--}}
                            {{--'id' => 'gradeId'--}}
                            {{--]) }}--}}
                            {{--</div>--}}
                            {{--@endif--}}
                        @endif
                    </div>
                    <!-- 所属班级 -->
                    <div class="form-group">
                        @if(isset($classes))
                            @include('partials.single_select', [
                                    'id' => 'classId',
                                    'label' => '所属班级',
                                    'items' => $classes
                                ])
                            {{--@if($classes->count() > 1)--}}
                            {{--@include('partials.single_select', [--}}
                            {{--'id' => 'classId',--}}
                            {{--'label' => '所属班级',--}}
                            {{--'items' => $classes--}}
                            {{--])--}}
                            {{--@else--}}
                            {{--{{ Form::label('classId', '所属班级', [--}}
                            {{--'class' => 'control-label col-sm-3'--}}
                            {{--]) }}--}}
                            {{--<div class="col-sm-6" style="padding-top: 8px;">--}}
                            {{--{{ $classes->first() }}--}}
                            {{--{{ Form::hidden('classId', $classes->keys()->first(), [--}}
                            {{--'id' => 'classId'--}}
                            {{--]) }}--}}
                            {{--</div>--}}
                            {{--@endif--}}
                        @endif
                    </div>
                    <!-- 学生列表 -->
                    <div class="form-group">
                        @if(isset($students))
                            @include('partials.single_select', [
                                    'id' => 'studentId',
                                    'label' => '被监护人',
                                    'items' => $students
                                ])
                            {{--@if($students->count() > 1)--}}
                            {{--@include('partials.single_select', [--}}
                            {{--'id' => 'studentId',--}}
                            {{--'label' => '被监护人',--}}
                            {{--'items' => $students--}}
                            {{--])--}}
                            {{--@else--}}
                            {{--{{ Form::label('studentId', '学生', [--}}
                            {{--'class' => 'control-label col-sm-3'--}}
                            {{--]) }}--}}
                            {{--<div class="col-sm-6" style="padding-top: 8px;">--}}
                            {{--{{ $students->first() }}--}}
                            {{--{{ Form::hidden('studentId', $students->keys()->first(), [--}}
                            {{--'id' => 'classId'--}}
                            {{--]) }}--}}
                            {{--</div>--}}
                            {{--@endif--}}
                        @endif
                    </div>
                    {{--<!-- 监护关系 -->--}}
                    <div class="form-group">
                        {{--@if(isset($students))--}}

                        {{--@endif--}}
                        {{ Form::label('relationship', '监护关系', [
                                'class' => 'control-label col-sm-3'
                            ]) }}
                        <div class="col-sm-6">
                            {{ Form::text('relationship', null, [
                                'id' => 'relationship',
                                'require' => 'true'
                            ]) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
                <a id="confirm-bind" href="#" class="btn btn-sm btn-success" data-dismiss="modal">确定</a>
            </div>
        </div>
    </div>
</div>
