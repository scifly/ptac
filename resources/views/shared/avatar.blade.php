<div class="form-group">
    {!! Form::label('avatar_url', '头像', [
        'class' => 'col-sm-3 control-label',
        'style' => 'line-height:80px'
    ]) !!}
    <div class="col-sm-6">
        <div class="input-group">
            <img src="{!! Auth::user()->avatar_url ?? asset('img/default.png') !!}"
                 class="img-circle" style="height: 80px;" alt="">
        </div>
        @if (!isset($student))
            <p class="help-block">（头像同步自用户的微信账号，此处不可更改）</p>
        @endif
    </div>
</div>