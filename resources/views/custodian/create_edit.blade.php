<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 监护人ID -->
            @if (!empty($custodian['id']))
                {{ Form::hidden('id', $custodian['id'], ['id' => 'id']) }}
                {{ Form::hidden('user_id', $custodian['user_id'], ['id' => 'user_id']) }}
                @include('shared.avatar', ['user' => $custodian->user])
            @endif
            <!-- 监护人姓名 -->
            <div class="form-group">
                {{ Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-user'])
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
                        @include('shared.icon_addon', ['class' => 'fa-language'])
                        {{ Form::text('user[english_name]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(可选)',
                            'type' => 'string',
                            'data-parsley-length' => '[2, 255]'
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 监护人性别 -->
            @include('shared.switch', [
                'label' => '性别',
                'id' => 'user[gender]',
                'value' => $custodian->user->gender ?? null,
                'options' => ['男', '女']
            ])
            <!-- 监护人手机列表 -->
            @include('shared.mobile')
            <!-- 监护人座机号码 -->
            <div class="form-group">
                {{ Form::label('user[telephone]', '座机', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-phone'])
                        {{ Form::text('user[telephone]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(可选)',
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
                        @include('shared.icon_addon', ['class' => 'fa-envelope-o'])
                        {{ Form::text('user[email]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(可选)',
                            'maxlength' => '255',
                            'data-parsley-type'=>"email"
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 监护关系 -->
            @include('custodian.custodian_student')
            <!-- 也是教职员工 -->
            @include('shared.switch', [
                'id' => 'singular',
                'value' => isset($custodian) ? ($custodian->user->educator ? 0 : 1) : null,
                'label' => '也是教职员工',
                'options' => ['否', '是']
            ])
            <!-- 监护人状态 -->
            @include('shared.switch', [
                'id' => 'user[enabled]',
                'value' => $custodian->user->enabled ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
