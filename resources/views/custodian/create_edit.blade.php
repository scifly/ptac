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
            @include('partials.switch', [
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
            <!-- 监护关系 -->
            @include('custodian.custodian_student', ['visible' => true])
            <!-- 单角色 -->
            @include('partials.switch', [
                'id' => 'singular',
                'value' => $custodian['singular'] ?? null,
                'label' => '单角色',
                'options' => ['是', '否']
            ])
            <!-- 班级、科目绑定关系 -->
            @include('educator.educator_class', ['visible' => false])
            <!-- 监护人状态 -->
            @include('partials.switch', [
                'id' => 'user[enabled]',
                'value' => $custodian->user->enabled ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
