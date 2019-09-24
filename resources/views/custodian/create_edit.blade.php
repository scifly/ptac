<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 监护人ID -->
            @if (isset($custodian))
                {!! Form::hidden('id', $custodian['id']) !!}
                {!! Form::hidden('user_id', $custodian['user_id']) !!}
                @include('shared.avatar', ['user' => $custodian->user])
            @endif
            <!-- 姓名 -->
            <div class="form-group">
                {!! Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-user'])
                        {!! Form::text('user[realname]', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'placeholder' => '(请填写真实姓名)',
                            'data-parsley-length' => '[2, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 性别 -->
            @include('shared.switch', [
                'label' => '性别',
                'id' => 'user[gender]',
                'value' => $custodian->user->gender ?? null,
                'options' => ['男', '女']
            ])
            <!-- 手机号码 -->
            <div class="form-group">
                {!! Form::label('user[mobile]', '手机号码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-mobile'])
                        {!! Form::text('user[mobile]', null, [
                            'type' => 'number',
                            'class' => 'form-control text-blue',
                            'data-parsley-length' => '[11,11]',
                            'data-parsley-pattern' => '/^1[3456789]\d{9}$/',
                            'placeholder' => '(选填。如果邮箱为空，此项必填)'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 电子邮件 -->
            <div class="form-group">
                {!! Form::label('user[email]', '邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-envelope-o'])
                        {!! Form::text('user[email]', null, [
                            'type'        => 'email',
                            'class'       => 'form-control text-blue',
                            'placeholder' => '(选填。如果手机号码为空，此项必填)',
                            'maxlength'   => '255',
                        ]) !!}
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
            @include('shared.card')
            <!-- 状态 -->
            @include('shared.switch', [
                'id' => 'user[enabled]',
                'value' => $custodian->user->enabled ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>