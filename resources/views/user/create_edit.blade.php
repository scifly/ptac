<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('username', '用户名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('username', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(用户名不能为空)',
                        'required' => 'true',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('avatar_url', '头像',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-7">
                    <div id="preview" style="display: block">
                        <img id="avatar_thumb_img"
                             @if(isset($user))src='{{asset("../storage/app/avauploads/{$user->avatar_url}")}}'
                             @else src="{{asset("../storage/app/avauploads/default_avatar.png")}}"
                             @endif
                             style="width:100px; height: 100px;max-width: 100px;max-height: 100px; border-radius:50%; overflow:hidden;"/>

                        <input @if(isset($user))id="{{$user->id}}" @else id="0" @endif
                        type="text" size="50" name="avatar_url" class="hide"
                               @if(isset($user)) value="{{$user->avatar_url}}" @else value="default_avatar.png" @endif/>

                        <a class="btn btn-upload"
                           style=" margin-left:50px; border:1px solid #3c8dbc; color:#3c8dbc !important; border-radius:40px; position: relative; overflow: hidden;">
                            <span>上传头像</span>
                            <input id="avatar_upload" type="file" name="file" multiple="multiple" style="position: absolute;
                             top: 0; right: 0; margin: 0; padding: 0; font-size: 20px; cursor: pointer; opacity: 0; filter: alpha(opacity=0);"/>
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('realname', '姓名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('realname', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(不得超过20个汉字)',
                        'required' => 'true',
                        'maxlength' => '60'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-4">
                    {!! Form::radio('gender', '1', true) !!}
                    {!! Form::label('gender', '男') !!}
                    {!! Form::radio('gender', '0') !!}
                    {!! Form::label('gender', '女') !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('group_id', '所属组别',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('group_id', $groups, null, ['class' => 'form-control text-blue']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('email', '电子邮箱',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('email', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(电子邮箱)',
                        'required' => 'true',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('wechatid', '微信号id',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('wechatid', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(小写字母和数字)',
                        'required' => 'true',
                        'data-parsley-type' => 'alphanum',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $user['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>