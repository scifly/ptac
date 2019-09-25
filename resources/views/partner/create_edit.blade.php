<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 合作伙伴id -->
            @if (isset($partner))
                {!! Form::hidden('id', $partner['id']) !!}
            @endif
            <!-- 全称 -->
            <div class="form-group">
                {!! Form::label('realname', '全称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-user-o'])
                        {!! Form::text('realname', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(必填。不超过60个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 60]',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 所属学校 -->
            @include('shared.single_select', [
                'id' => 'school_id',
                'label' => '所属学校',
                'items' => $schools,
                'icon' => 'fa fa-university text-purple'
            ])
            <!-- 接口用户名 -->
            <div class="form-group">
                {!! Form::label('username', '接口用户名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-user'])
                        {!! Form::text('username', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(必填)',
                            'required' => 'true',
                            'data-parsley-length' => '[6, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 接口密码 -->
            <div class="form-group">
                {!! Form::label('secret', '接口密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-lock'])
                        {!! Form::text('secret', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(必填)',
                            'required' => 'true',
                            'data-parsley-length' => '[6, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 接口类名 -->
            <div class="form-group">
                {!! Form::label('classname', '接口类名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>类</strong>
                        </div>
                        {!! Form::text('classname', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(必填)',
                            'required' => 'true',
                            'data-parsley-length' => '[6, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 联系人 -->
            <div class="form-group">
                {!! Form::label('contact', '联系人', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-phone'])
                        {!! Form::text('contact', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(选填)',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 手机号码 -->
            <div class="form-group">
                {!! Form::label('mobile', '手机号码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-mobile'])
                        {!! Form::text('mobile', null, [
                            'type' => 'number',
                            'class' => 'form-control text-blue',
                            'data-parsley-length' => '[11,11]',
                            'data-parsley-pattern' => '/^1[3456789]\d{9}$/',
                            'placeholder' => '(选填)'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 电子邮箱 -->
            <div class="form-group">
                {!! Form::label('email', '电子邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-envelope-o'])
                        {!! Form::text('email', null, [
                            'type' => 'email',
                            'class' => 'form-control text-blue',
                            'placeholder' => '(选填)',
                            'maxlength' => '255'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 状态 -->
            @include('shared.switch', [

                'value' => $partner['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>