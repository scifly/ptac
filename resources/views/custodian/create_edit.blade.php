<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($custodian['id']))
                {{ Form::hidden('id', $custodian['id'], ['id' => 'id']) }}
            @endif
            @if (!empty($custodian['user_id']))
                 {{ Form::hidden('user_id', $custodian['user_id'], ['id' => 'user_id']) }}
            @endif
                {{--<div class="form-group">--}}
                    {{--{{ Form::label('user[username]', '用户名', [--}}
                        {{--'class' => 'col-sm-3 control-label'--}}
                    {{--]) }}--}}
                    {{--<div class="col-sm-6">--}}
                        {{--{{ Form::text('user[username]', null, [--}}
                            {{--'class' => 'form-control',--}}
                            {{--'required' => 'true',--}}
                            {{--'placeholder' => '(请填写用户名)',--}}
                            {{--'data-parsley-length' => '[2, 255]'--}}
                        {{--]) }}--}}
                    {{--</div>--}}
                {{--</div>--}}
            <div class="form-group">
                {{ Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    {{ Form::text('user[realname]', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'placeholder' => '(请填写真实姓名)',
                        'data-parsley-length' => '[2, 255]'
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
                        'data-parsley-type' => 'alphanum',
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
                <div class="form-group">
                    <label for="mobile[mobile][]" class="col-sm-3 control-label">手机号码</label>
                    <div class="col-sm-6">
                        <table class="table-bordered table-responsive" style="width: 100%;">
                            <thead>
                            <tr>
                                {{--<td><label for="mobile[mobile][]">手机号码</label></td>--}}
                                <td>手机号码</td>
                                {{--<td style="text-align: center;"><label for="mobile[isdefault][]">默认</label></td>--}}
                                <td style="text-align: center;">默认</td>
                                {{--<td style="text-align: center;"><label for="mobile[enabled][]">启用</label></td>--}}
                                <td style="text-align: center;">启用</td>
                                <td></td>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($custodian->user->mobiles))
                                @foreach($custodian->user->mobiles as $key => $mobile)
                                    <tr>
                                        <td><input class="form-control" name="mobile[mobile][e{{$key}}]" type="text"
                                                   placeholder="（请输入手机号码）" value='{{$mobile->mobile}}'>
                                        </td>
                                        <td style="text-align: center;">
                                            <input name="mobile[isdefault]" value="e{{$key}}" type="radio" class="minimal" @if($mobile->isdefault == 1) checked @endif/>
                                        </td>
                                        <td style="text-align: center;">
                                            <input name="mobile[enabled][e{{$key}}]" type="checkbox" class="minimal" @if($mobile->enabled == 1) checked @endif />
                                        </td>
                                        <td style="text-align: center;">
                                        <span class="input-group-btn">
                                            <button class="btn btn-box-tool btn-add" type="button">
                                                <i class="fa fa-plus text-blue"></i>
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
                        'maxlength' => '255',
                        'data-parsley-type'=>"email"
                    ]) }}
                </div>
            </div>
            @include('partials.multiple_select', [

                'label' => '所属部门',

                'id' => 'department_ids',
                'items' => $departments,
                'selectedItems' => isset($selectedDepartments) ? $selectedDepartments : NULL
            ])
            @include('partials.single_select', [
                'label' => '角色',
                'id' => 'user[group_id]',
                'items' => $groups,
            ])
                @include('partials.multiple_select', [
               'label' => '包含学生',
               'id' => 'student_ids',
               'items' => $students,
               'selectedItems' => isset($selectedStudents) ? $selectedStudents : NULL
           ])
            <div class="form-group addInput">
                    @if(isset($custodianStudent)&& !empty($custodianStudent))
                        @foreach($custodianStudent as $key=>$value)
                            @if($key==0)
                                <label for="relationship" class="col-sm-3 control-label">和学生之间的关系</label>
                            @endif
                            <div class="entry input-group col-sm-6 col-sm-offset-3">
                                <input type="text" class="form-control" name="relationship[]"
                                       value="{{$value['relationship']}}">
                                <span class="input-group-btn">
                                <button class="btn btn-add2 btn-success" type="button">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </button>
                            </span>
                            </div>
                        @endforeach
                    @else
                        <label for="relationship" class="col-sm-3 control-label">和监护人之间的关系</label>
                        <div class="entry input-group col-sm-6">
                            <input type="text" class="form-control" name="relationship[]">
                            <span class="input-group-btn">
                            <button class="btn btn-add2 btn-success" type="button">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        </span>
                        </div>
                    @endif
                </div>
            <div class="form-group">
                {{ Form::label('expiry', '服务到期时间', [
                'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    {!! Form::text('expiry', null, [
                        'class' => 'form-control expiry-date',
                    ]) !!}
                </div>
            </div>

            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'user[enabled]',
                'value' => isset($user['user']['enabled']) ? $user['user']['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
