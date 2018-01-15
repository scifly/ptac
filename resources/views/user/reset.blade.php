@isset($reset)
    <script src="{{ URL::asset($reset) }}"></script>
@endisset

{!! Form::open([
    'method' => 'post',
    'id' => 'formUser',
    'class' => 'form-horizontal form-bordered',
    'data-parsley-validate' => 'true'
]) !!}
<section class="content clearfix">
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
            <div class="box box-default box-solid">
                <div class="box-header with-border">
                    <span id="breadcrumb" style="color: #999; font-size: 13px;">用户中心/重置密码</span>
                    <div class="box-tools pull-right">
                        <button id="record-list" type="button" class="btn btn-box-tool">
                            <i class="fa fa-mail-reply text-blue"> 返回列表</i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-horizontal">
                        @if (isset($user['id']))
                            {{ Form::hidden('user_id', $user['id'], ['id' => 'user_id']) }}
                        @endif
                        <div class="form-group">
                            {!! Form::label('password', '请输入原密码', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                    {!! Form::password('password', [
                                        'class' => 'form-control',
                                        'placeholder' => '(请输入密码)',
                                        'required' => 'true',
                                        'minlength' => '6'
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
                                    <div class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                    {!! Form::password('password', [
                                        'class' => 'form-control',
                                        'placeholder' => '(请输入密码)',
                                        'required' => 'true',
                                        'minlength' => '6'
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('password', '请确认新密码', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                    {!! Form::password('password', [
                                        'class' => 'form-control',
                                        'placeholder' => '(请确认密码)',
                                        'required' => 'true',
                                        'minlength' => '6'
                                    ]) !!}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                @include('partials.form_overlay')
                <div class="box-footer">
                    {{--button--}}
                    <div class="form-group">
                        <div class="col-sm-3 col-sm-offset-3">
                            {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'reset']) !!}
                            {!! Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{!! Form::close() !!}


