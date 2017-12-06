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

            @if (isset($user['user_id']))
                {{ Form::hidden('user_id', $user['user_id'], ['id' => 'user_id']) }}
            @endif

            <div class="form-group">
                {!! Form::label('password', '密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-lock"></i>
                        </div>
                        {!! Form::password('user[password]', [
                            'class' => 'form-control',
                            'placeholder' => '(请输入密码)',
                            'required' => 'true',
                            'minlength' => '8'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('user[password_confirmation]', '确认密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-lock"></i>
                        </div>
                        {!! Form::password('password_confirmation', [
                            'class' => 'form-control',
                            'placeholder' => '(请确认密码)',
                            'required' => 'true',
                            'minlength' => '8'
                        ]) !!}
                    </div>
                </div>
            </div>


                @include('partials.enabled', [
                    'id' => 'enabled',
                    'value' => isset($user['enabled']) ? $user['enabled'] : NULL
                ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
{!! Form::close() !!}


