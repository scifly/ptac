<link rel="stylesheet" href="{{ URL::asset('js/plugins/parsley/parsley.css') }}">
{!! Form::model(Auth::user(), [ 'method' => 'PUT', 'id' => 'formUser', 'data-parsley-validate' => 'true']) !!}
<section class="content clearfix">
    @include('partials.modal_delete')
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
            <div class="box box-default box-solid">
                <div class="box-header with-border">
                    <span id="breadcrumb" style="color: #999; font-size: 13px;">用户中心/修改个人信息</span>
                </div>
                <div class="box-body">
                    <div class="form-horizontal">

                        {{ Form::hidden('id', Auth::user()->id, ['id' => 'id']) }}
                        <div class="form-group">
                            {!! Form::label('avatar_url', '头像', [
                                'class' => 'col-sm-3 control-label',
                                'style' =>'line-height:80px'
                            ]) !!}
                            <div class="col-sm-6">
                                <div class="input-group">
                                    @if (Auth::user()->avatar_url && file_exists(Auth::user()->avatar_url))
                                        <img src="{!! Auth::user()->avatar_url !!}" style="height: 80px;border-radius: 40px;">
                                    @else
                                        <img src="{!! asset('img/user2-160x160.jpg') !!}" style="height: 80px;border-radius: 40px;">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('realname', '姓名', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                <div class="input-group">
                                    @include('partials.icon_addon', ['class' => 'fa-user'])
                                    {!! Form::text('realname', null, [
                                        'class' => 'form-control text-blue',
                                        'placeholder' => '(请输入真实姓名)',
                                        'required' => 'true',
                                        'data-parsley-length' => '[2,10]',
                                        'disabled' => 'true'
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('username', '用户名', [
                                'class' => 'col-sm-3 control-label',
                            ]) !!}
                            <div class="col-sm-6">
                                <div class="input-group">
                                    @include('partials.icon_addon', ['class' => 'fa-user-o'])
                                    {!! Form::text('username', null, [
                                        'class' => 'form-control text-blue',
                                        'placeholder' => '(请输入用户名)',
                                        'required' => 'true',
                                        'readonly' => 'true',
                                        'data-parsley-length' => '[6,20]'
                                    ]) !!}
                                    <a class="edit_input"
                                       style="position: absolute;top: 0;right: -25px;line-height:34px" title="编辑"
                                       href="#">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {{ Form::label('english_name', '英文名', [
                                'class' => 'col-sm-3 control-label'
                            ]) }}
                            <div class="col-sm-6">
                                <div class="input-group">
                                    @include('partials.icon_addon', ['class' => 'fa-language'])
                                    {{ Form::text('english_name', null, [
                                        'class' => 'form-control text-blue',
                                        'placeholder' => '请填写英文名(可选)',
                                        'data-parsley-type' => 'alphanum',
                                        'data-parsley-length' => '[2, 64]',
                                        'readonly'=> 'true',
                                    ]) }}
                                    <a class="edit_input"
                                       style="position: absolute;top: 0;right: -25px;line-height:34px" title="编辑"
                                       href="#">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- 性别 -->
                        @include('partials.switch', [
                            'id' => 'gender',
                            'label' => '性别',
                            'value' => $user->gender ?? null,
                            'options' => ['男', '女']
                        ])

                        <div class="form-group">
                            {{ Form::label('telephone', '座机', [
                                'class' => 'col-sm-3 control-label'
                            ]) }}
                            <div class="col-sm-6">
                                <div class="input-group">
                                    @include('partials.icon_addon', ['class' => 'fa-phone'])
                                    {{ Form::text('telephone', null, [
                                        'class' => 'form-control text-blue',
                                        'placeholder' => '请输入座机号码(可选}',
                                       'readonly' => 'true',
                                    ]) }}
                                    <a class="edit_input"
                                       style="position: absolute;top: 0;right: -25px;line-height:34px" title="编辑"
                                       href="#">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('email', '电子邮箱', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                <div class="input-group">
                                    @include('partials.icon_addon', ['class' => 'fa-envelope-o'])
                                    {!! Form::email('email', null, [
                                        'class' => 'form-control text-blue',
                                        'placeholder' => '(请输入电子邮件地址)',
                                        'readonly' => 'true',
                                    ]) !!}
                                    <a class="edit_input"
                                       style="position: absolute;top: 0;right: -25px;line-height:34px" title="编辑"
                                       href="#">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{!! Form::close() !!}

<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/parsley.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/i18n/zh_cn.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/i18n/zh_cn.extra.js') }}"></script>
<script src="{{ URL::asset('js/plugins/gritter/js/jquery.gritter.js') }}"></script>
@isset($profile)
    <script src="{{ URL::asset($profile) }}"></script>
@endisset


