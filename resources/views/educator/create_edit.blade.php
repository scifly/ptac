{!! Form::open([
    'method' => 'post',
    'id' => 'formEducator',
    'class' => 'form-horizontal form-bordered',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($educator['id']))
                {{ Form::hidden('id', $educator['id'], ['id' => 'id']) }}
            @endif
            @if (isset($educator['user_id']))
                {{ Form::hidden('user_id', $educator['user_id'], ['id' => 'user_id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('user[realname]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入真实姓名)',
                        'required' => 'true',
                        'data-parsley-length' => '[2,10]'
                    ]) !!}
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
                        'data-parsley-type' => 'alphanum',
                        'data-parsley-length' => '[2, 255]'
                    ]) }}
                </div>
            </div>
            <div class="form-group">
                <label for="user[gender]" class="col-sm-3 control-label">性别</label>
                <div class="col-sm-6">
                    <label id="user[gender]">
                        <input id="user[gender]" @if(isset($educator)) @if($educator->user->gender == 1) checked @endif @endif type="radio" name="user[gender]" class="minimal" value="1">
                    </label> 男
                    <label id="user[gender]">
                        <input id="user[gender]" @if(isset($educator)) @if($educator->user->gender == 0) checked @endif @endif type="radio" name="user[gender]" class="minimal" value="0">
                    </label> 女
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('user[username]', '用户名', [
                    'class' => 'col-sm-3 control-label',
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('user[username]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入用户名)',
                        'required' => 'true',
                        'data-parsley-length' => '[6,20]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('user[password]', '密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::password('user[password]', [
                        'class' => 'form-control',
                        'placeholder' => '(请输入密码)',
                        'required' => 'true',
                        'minlength' => '8'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('user[password_confirm]', '确认密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::password('user[password_confirm]', [
                        'class' => 'form-control',
                        'placeholder' => '(请确认密码)',
                        'required' => 'true',
                        'minlength' => '8'
                    ]) !!}
                </div>
            </div>
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
                {!! Form::label('user[email]', '电子邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('user[email]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入电子邮件地址)',
                        'data-parsley-length' => '[3,255]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="mobile[mobile][]" class="col-sm-3 control-label">手机号码</label>
                <div class="col-sm-6">
                    <table id="mobileTable" class="table-bordered table-responsive" style="width: 100%;">
                        <thead>
                        <tr>
                            <td>手机号码</td>
                            <td style="text-align: center;">默认</td>
                            <td style="text-align: center;">启用</td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($educator->user->mobiles))
                            @foreach($educator->user->mobiles as $key => $mobile)
                                <tr>
                                    <td><input class="form-control" name="mobile[{{$key}}][mobile]"
                                               placeholder="（请输入手机号码）" value='{{$mobile->mobile}}' pattern="/^1[0-9]{10}$/">
                                        <input class="form-control" name="mobile[{{$key}}][id]"
                                               type="hidden" value='{{$mobile->id}}'>
                                    </td>
                                    <td style="text-align: center;">
                                        <input name="mobile[isdefault]" value="{{$key}}" type="radio" class="minimal"
                                               @if($mobile->isdefault == 1) checked @endif/>
                                    </td>
                                    <td style="text-align: center;">
                                        <input name="mobile[{{$key}}][enabled]" type="checkbox" class="minimal" value="{{$mobile->enabled}}"
                                               @if($mobile->enabled == 1) checked @endif />
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="input-group-btn">
                                            <button class="btn btn-box-tool btn-remove" type="button">
                                                <i class="fa fa-minus text-blue"></i>
                                            </button>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td><input class="form-control" name="mobile[mobile][e1]" type="text"
                                           placeholder="（请输入手机号码）"></td>
                                <td style="text-align: center;">
                                    <input name="mobile[isdefault]" value="e1" type="radio" class="minimal">
                                </td>
                                <td style="text-align: center;">
                                    <input name="mobile[enabled][e1]" type="checkbox" class="minimal">
                                </td>
                                <td style="text-align: center;">
                                        <span class="input-group-btn">
                                            <button class="btn btn-box-tool btn-add" type="button">
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
                'label' => '角色',
                'id' => 'user[group_id]',
                'items' => $groups
            ])

            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'educator[school_id]',
                'items' => $schools
            ])
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
                        <input type="hidden" id="selectedDepartmentIds"  value="{{$selectedDepartmentIds}}" />
                    @else
                        <input type="hidden" id="selectedDepartmentIds"  value="" />
                    @endif
                    <a id="add-department" class="btn btn-primary" style="margin-bottom: 5px">修改</a>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">空</label>
                <div class="col-sm-6">
                    <table id="classTable" class="table-bordered table-responsive" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>班级</th>
                            <th>科目</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

                        @if(isset($educator->educatorClasses))
                            @foreach($educator->educatorClasses  as $index=> $class)
                                <tr>
                                    <td>
                                        <select name="classSubject[{{$index}}][class_id]" class="select2" style="width: 80%;">
                                            @foreach($squads as $key => $squad )
                                                    <option value='{{$key}}' @if($key == $class->class_id) selected="selected" @endif>{{$squad}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="classSubject[{{$index}}][subject_id]" class="select2" style="width: 80%">
                                            @foreach($subjects as $key => $subject )
                                                <option value='{{$key}}' @if($key == $class->subject_id) selected="selected" @endif>{{$subject}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="text-align: center">
                                    <span class="input-group-btn">
                                        <button class="btn btn-box-tool btn-class-remove" type="button">
                                            <i class="fa fa-minus text-blue"></i>
                                        </button>
                                    </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>
                                    <select name="educator[class_ids][]" class="select2" style="width: 80%;">
                                        @foreach($squads as $key => $squad )
                                            <option value='{{$key}}'>{{$squad}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="educator[subject_ids][]" class="select2" style="width: 80%">
                                        @foreach($subjects as $key => $subject )
                                            <option value='{{$key}}'>{{$subject}}</option>
                                        @endforeach
                                    </select>
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
            @include('partials.multiple_select', [
               'label' => '所属组',
               'id' => 'educator[team_id]',
               'items' => $teams,
               'selectedItems' => isset($selectedTeams) ? $selectedTeams : []

           ])
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'user[enabled]',
                'value' => isset($educator['enabled']) ? $educator['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
{!! Form::close() !!}


