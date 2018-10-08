<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 监护人ID -->
            @if (!empty($custodian['id']))
                {{ Form::hidden('id', $custodian['id'], ['id' => 'id']) }}
                {{ Form::hidden('user_id', $custodian['user_id'], ['id' => 'user_id']) }}
                @include('partials.avatar', ['user' => $custodian->user])
            @endif
            <!-- 监护人姓名 -->
            <div class="form-group">
                {{ Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-user'])
                        {{ Form::text('user[realname]', null, [
                            'class' => 'form-control text-blue',
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
                        @include('partials.icon_addon', ['class' => 'fa-language'])
                        {{ Form::text('user[english_name]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请填写英文名, 可选)',
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
                'value' => $custodian->user->gender ?? null,
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
                        @include('partials.icon_addon', ['class' => 'fa-phone'])
                        {{ Form::text('user[telephone]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入座机号码, 可选)',
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
                        @include('partials.icon_addon', ['class' => 'fa-envelope-o'])
                        {{ Form::text('user[email]', null, [
                            'class' => 'form-control text-blue',
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
                    <div style="display: block; overflow-x: auto; clear: both; width: 100%; margin-top: 10px;">
                        <table class="table table-striped table-bordered table-hover table-condensed"
                               style="white-space: nowrap; width: 100%;">
                            <thead>
                            <tr class="bg-info">
                                <th>学生</th>
                                <th>学号</th>
                                <th>监护关系</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody id="tBody">
                            @if(!empty($relations))
                                @foreach($relations as $key => $relation)
                                    <tr>
                                        <td>
                                            <input type="hidden" value="{{ $relation->student_id }}"
                                                   name="student_ids[{{ $key }}]" id="student_ids"
                                            >
                                            {{ $relation->student->user->realname }}
                                        </td>
                                        <td>
                                            {{ $relation->student->student_number }}
                                        </td>
                                        <td>
                                            <label for=""></label>
                                            <input type="text" name="relationships[{{ $key }}]" id="" readonly
                                                   class="no-border" style="background: none;"
                                                   value="{{ $relation->relationship }}"
                                            >
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
                    </div>
                    <button id="add" class="btn btn-box-tool" type="button">
                        <i class="fa fa-user-plus text-blue">&nbsp;新增</i>
                    </button>
                </div>
            </div>
            <!-- 监护人状态 -->
            @include('partials.enabled', [
                'id' => 'user[enabled]',
                'value' => $custodian->user->enabled ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
