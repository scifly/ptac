{!! Form::open([
    'method' => 'post',
    'id' => 'formStudent',
    'class' => 'form-horizontal form-bordered',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($student['id']))
                {{ Form::hidden('id', $student['id'], ['id' => 'id']) }}
            @endif
            @if (!empty($student['user_id']))
                {{ Form::hidden('user_id', $student['user_id'], ['id' => 'user_id']) }}
            @endif
            <div class="form-group">
                {{ Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    {{ Form::text('user[realname]', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'placeholder' => '(请填写真实姓名)',
                        'data-parsley-length' => '[2, 30]'
                    ]) }}
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('user[english_name]', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    {{ Form::text('user[english_name]', null, [
                        'class' => 'form-control',
                        'placeholder' => '请填写英文名(可选)',
                        'type' => 'string',
                        'data-parsley-length' => '[2, 255]'
                    ]) }}
                </div>
            </div>
            <div class="form-group">
                <label for="user[gender]" class="col-sm-3 control-label">性别</label>
                <div class="col-sm-6">
                    {!! Form::radio('user[gender]', '1', true) !!}
                    {!! Form::label('user[gender]', '男') !!}
                    {!! Form::radio('user[gender]', '0') !!}
                    {!! Form::label('user[gender]', '女') !!}
                </div>
            </div>
            @include('partials.mobile')
            <div class="form-group">
                {{ Form::label('user[telephone]', '座机', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    {{ Form::text('user[telephone]', null, [
                        'class' => 'form-control',
                        'placeholder' => '请输入座机号码(可选}',
                    ]) }}
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('user[email]', '邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
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

            <div class="form-group">
                {!! Form::label('departmentId', '所属部门', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div id="department-nodes-checked">
                        @if(isset($selectedDepartments))
                            @foreach($selectedDepartments as $key => $department)
                                <button type="button" class="btn btn-flat" style="margin-right: 5px;margin-bottom: 5px">
                                    <i class="{{$department['icon']}}"></i>
                                    {{$department['text']}}
                                    <i class="fa fa-close close-selected"></i>
                                    <input type="hidden" name="selectedDepartments[]" value="{{$department['id']}}"/>
                                </button>
                            @endforeach

                        @endif
                    </div>
                    @if(isset($selectedDepartmentIds))
                        <input type="hidden" id="selectedDepartmentIds" value="{{$selectedDepartmentIds}}"/>
                    @else
                        <input type="hidden" id="selectedDepartmentIds" value=""/>
                    @endif
                    <a id="add-department" class="btn btn-primary" style="margin-bottom: 5px">修改</a>
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '角色',
                'id' => 'user[group_id]',
                'items' => $groups,
            ])
            <div class="form-group">
                <label class="col-sm-3 control-label">监护人和学生之间的关系</label>
                <div class="col-sm-6">
                    <table id="classTable" class="table-bordered table-responsive" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>监护人</th>
                            <th>关系</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

                        @if(isset($student->custodianStudents))
                            @foreach($student->custodianStudents as $custodian)
                                <tr>
                                    <td>
                                        <select name="student_ids[]" class="select2" style="width: 80%;">
                                            @foreach($custodians as $key => $name )
                                                <option value='{{$key}}'
                                                        @if($key == $custodian->custodian_id) selected="selected" @endif>{{$name}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="relationship[]"
                                               value="{{$custodian->relationship}}">
                                    </td>
                                    <td style="text-align: center">
                                    <span class="input-group-btn">
                                        <button class="btn btn-box-tool btn-class-add" type="button">
                                            <i class="fa fa-plus text-blue"></i>
                                        </button>
                                    </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>
                                    <select name="custodian_ids[]" class="select2" style="width: 80%;">
                                        @foreach($custodians as $key => $name )
                                            <option value='{{$key}}'>{{$name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="relationship[]" class='form-control' required="true"
                                           placeholder='请填写监护人和学生之间的关系'>
                                </td>
                                <td style="text-align: center">
                                    <span class="input-group-btn">
                                        <button class="btn btn-box-tool btn-class-add" type="button">
                                            <i class="fa fa-plus text-blue"></i>
                                        </button>
                                    </span>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

            @include('partials.single_select', [
                'label' => '班级名称',
                'id' => 'student[class_id]',
                'items' => $class
            ])
            <div class="form-group">
                {!! Form::label('student[student_number]', '学号', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('student[student_number]', null, [
                        'class' => 'form-control',
                        'placeholder' => '小写字母与阿拉伯数字',
                        'data-parsley-type' => 'alphanum',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 32]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('student[card_number]', '卡号', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('student[card_number]', null, [
                        'class' => 'form-control',
                        'placeholder' => '小写字母与阿拉伯数字',
                        'required' => 'true',
                        'data-parsley-type' => 'alphanum',
                        'data-parsley-length' => '[2, 32]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('student[birthday]', '生日', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('student[birthday]', null, [
                        'required' => 'true',
                        'class' => 'form-control',
                        'placeholder' => '生日格式为2000-08-12形式',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('student[remark]', '备注', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('student[remark]', null, [
                        'class' => 'form-control',
                        'placeholder' => '备注',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 32]'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '是否住校',
                'id' => 'student[oncampus]',
                'value' => isset($student['oncampus']) ? $student['oncampus'] : NULL,
            ])
            @include('partials.enabled', [
                'label' => '状态',
                'id' => 'user[enabled]',
                'value' => isset($student['enabled']) ? $student['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
