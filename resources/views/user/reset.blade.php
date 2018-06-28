{!! Form::open([
    'method' => 'post',
    'id' => 'formUser',
    'class' => 'form-horizontal form-bordered',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($user['id']))
                {{ Form::hidden('user_id', $user['id'], ['id' => 'user_id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('old_password', '请输入原密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-lock'])
                        {!! Form::password('old_password', [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入密码)',
                            'required' => 'true',
                            'minlength' => '6',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('password', '请输入新密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-lock'])
                        {!! Form::password('password', [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入密码)',
                            'required' => 'true',
                            'minlength' => '6',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('confirm_password', '请确认新密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-lock'])
                        {!! Form::password('confirm_password', [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请确认密码)',
                            'required' => 'true',
                            'minlength' => '6',
                            'data-parsley-equalto' => '#password'
                        ]) !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
    {{--@include('partials.form_overlay')--}}
    {{--<div class="box-footer">--}}
        {{--button--}}
        {{--<div class="form-group">--}}
            {{--<div class="col-sm-3 col-sm-offset-3">--}}
                {{--{!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'reset']) !!}--}}
                {{--{!! Form::reset('重置', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    @include('partials.form_buttons')

</div>
{!! Form::close() !!}


