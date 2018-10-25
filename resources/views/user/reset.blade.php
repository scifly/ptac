{!! Form::open([
    'method' => 'post',
    'id' => 'formUser',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($user['id']))
                {{ Form::hidden('user_id', $user['id'], ['id' => 'user_id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('old_password', '原密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-lock'])
                        {!! Form::password('old_password', [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请在此输入原密码)',
                            'required' => 'true',
                            'minlength' => '6',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('password', '新密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-lock'])
                        {!! Form::password('password', [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入新密码)',
                            'required' => 'true',
                            'minlength' => '8',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('password_confirmation', '确认新密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-lock'])
                        {!! Form::password('password_confirmation', [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请再次输入新密码)',
                            'required' => 'true',
                            'minlength' => '8',
                            'data-parsley-equalto' => '#password'
                        ]) !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('shared.form_buttons', ['label' => '重置'])
</div>
{!! Form::close() !!}


