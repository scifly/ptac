{!! Form::model($user, [
    'method' => 'put',
    'id' => 'formUser',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 用户名 -->
            <div class="form-group">
                {!! Form::label('username', '用户名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-user'])
                        {!! Form::text('username', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(用户名不能为空)',
                            'required' => 'true',
                            'data-parsley-length' => [6, 255]
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 真实姓名 -->
            <div class="form-group">
                {!! Form::label('realname', '真实姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-user-o'])
                        {!! Form::text('realname', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不超过10个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => [2, 10],
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 英文名 -->
            <div class="form-group">
                {!! Form::label('english_name', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-language'])
                        {!! Form::text('english_name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(可选)',
                            'data-parsley-length' => [2, 20],
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
                        @include('partials.icon_addon', ['class' => 'fa-mobile'])
                        {!! Form::text('mobile', $mobile, [
                            'class' => 'form-control text-blue',
                            'data-parsley-length' => [11, 11],
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 电子邮箱 -->
            <div class="form-group">
                {{ Form::label('email', '邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-envelope-o'])
                        {{ Form::text('email', null, [
                            'class' => 'form-control text-blue',
                            'type' => 'email',
                            'maxlength' => '255',
                            'data-parsley-type'=>"email"
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 座机号码 -->
            <div class="form-group">
                {{ Form::label('telephone', '座机', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-phone'])
                        {{ Form::text('telephone', null, [
                            'class' => 'form-control text-blue',
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 性别 -->
            @include('partials.enabled', [
                'id' => 'gender',
                'label' => '性别',
                'value' => $user['gender'],
                'options' => ['男', '女']
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
{!! Form::close() !!}