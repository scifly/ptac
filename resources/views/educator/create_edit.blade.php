{!! Form::open([
    'method' => 'post',
    'id' => 'formEducator',
    'class' => 'form-horizontal form-bordered',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
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
                {{ Form::label('user[wechatid]', '微信号', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    {{ Form::text('user[wechatid]', null, [
                        'class' => 'form-control',
                        'data-parsley-type' => 'alphanum',
                        'data-parsley-length' => '[2, 255]'
                    ]) }}
                </div>
            </div>
            <div class="form-group">
                <label for="user[gender]" class="col-sm-3 control-label">性别</label>
                <div class="col-sm-6">
                    <label id="user[gender]">
                        <input id="user[gender]"
                               @if((isset($educator) && $educator->user->gender) || !isset($educator))
                               checked
                               @endif
                               type="radio" name="user[gender]" class="minimal" value="1">
                    </label> 男
                    <label id="user[gender]">
                        <input id="user[gender]"
                               @if((isset($educator) && $educator->user->gender ==0 ))
                               checked
                               @endif
                               type="radio" name="user[gender]" class="minimal" value="0">
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
            @if ( !isset($educator['id']))
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
                    {!! Form::label('user[password_confirmation]', '确认密码', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        {!! Form::password('user[password_confirmation]', [
                            'class' => 'form-control',
                            'placeholder' => '(请确认密码)',
                            'required' => 'true',
                            'minlength' => '8'
                        ]) !!}
                    </div>
                </div>
            @endif
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
                    {!! Form::email('user[email]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入电子邮件地址)',

                    ]) !!}
                </div>
            </div>
            @include('partials.mobile')
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
                        <input type="hidden" id="selectedDepartmentIds" value="{{$selectedDepartmentIds}}"/>
                    @else
                        <input type="hidden" id="selectedDepartmentIds" value=""/>
                    @endif
                    <a id="add-department" class="btn btn-primary" style="margin-bottom: 5px">修改</a>
                </div>
            </div>
            @include('educator.class_subject')

            @include('partials.multiple_select', [
               'label' => '所属组',
               'id' => 'educator[team_id]',
               'items' => $teams,
               'selectedItems' => isset($selectedTeams) ? $selectedTeams : []

           ])
            @include('partials.enabled', [
                'id' => 'user[enabled]',
                'value' => isset($educator['enabled']) ? $educator['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
{!! Form::close() !!}


