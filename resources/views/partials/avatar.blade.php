<div class="form-group">
    {!! Form::label('avatar_url', '头像', [
        'class' => 'col-sm-3 control-label',
        'style' => 'line-height:80px'
    ]) !!}
    <div class="col-sm-6">
        <div class="input-group">
            <img src="{{ $user->avatar_url ? $user->avatar_url : asset('img/user2-160x160.jpg') }}"
                 class="img-circle" style="height: 80px;">
        </div>
        <p class="help-block">（同步自用户的微信账号，此处不可更改）</p>
    </div>
</div>