<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 合作伙伴id -->
            @if (!empty($partner['id']))
                {{ Form::hidden('id', $partner['id'], ['id' => 'id']) }}
            @endif
            <!-- 全称 -->
            <div class="form-group">
                {!! Form::label('realname', '全称', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-user-o'])
                        {!! Form::text('realname', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不超过60个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 60]',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 接口用户名 -->
            <div class="form-group">
                {!! Form::label('username', '接口用户名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-user'])
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
                {!! Form::label('english_name', '接口密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-lock'])
                        {!! Form::text('english_name', null, [
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
                {!! Form::label('position', '接口类名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>类</strong>
                        </div>
                        {!! Form::text('position', null, [
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
                {{ Form::label('telephone', '联系人', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-phone'])
                        {{ Form::text('telephone', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入联系人及电话, 可选)',
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 电子邮箱 -->
            <div class="form-group">
                {{ Form::label('email', '电子邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-envelope-o'])
                        {{ Form::text('email', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入电子邮件地址, 可选)',
                            'type' => 'email',
                            'maxlength' => '255',
                            'data-parsley-type'=>"email"
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 状态 -->
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $partner['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>