<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('username', '用户名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('username', null, [
                        'class' => 'form-control',
                        'placeholder' => '(用户名不能为空)',
                        'data-parsley-required' => 'true',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('avatar_url', '头像',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-6">
                    @if(isset($user->avatar_url))
                            <img src="../../..{{$user->avatar_url}}">
                            <input type="hidden" name="avatar_url" value="{{$user->avatar_url}}"/>
                    @endif
                <div class="preview" style="width: 100px;overflow: hidden;"></div>
                <a class="btn btn-primary" data-toggle="modal" data-target="#modalPic">上传</a>
            </div>
            <div class="form-group">
                {!! Form::label('realname', '姓名',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('realname', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过20个汉字)',
                        'data-parsley-required' => 'true',
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
                    {!! Form::select('group_id', $groups, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('email', '电子邮箱',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('email', null, [
                        'class' => 'form-control',
                        'placeholder' => '(电子邮箱)',
                        'data-parsley-required' => 'true',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('wechatid', '微信号id',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('wechatid', null, [
                        'class' => 'form-control',
                        'placeholder' => '(小写字母和数字)',
                        'data-parsley-required' => 'true',
                        'data-parsley-type' => 'alphanum',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-4">
                    {!! Form::radio('enabled', '1', true) !!}
                    {!! Form::label('enabled', '启用') !!}
                    {!! Form::radio('enabled', '0') !!}
                    {!! Form::label('enabled', '禁用') !!}
                </div>
            </div>

        </div>
    </div>
    <div class="box-footer">
        {{--button--}}
        <div class="form-group">
            <div class="col-sm-3 col-sm-offset-4">
                {!! Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'save']) !!}
                {!! Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']) !!}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalPic">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">×
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    头像上传
                </h4>
            </div>
            <div class="modal-body">
                <form action="#" class="form-horizontal" enctype="multipart/form-data">
                    <input type="file" id="uploadFile" accept="image/jpeg,image/gif,image/png" multiple>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">关闭
                </button>
                <button type="button" class="btn btn-primary" id="upload">
                    上传
                </button>
            </div>
        </div>
    </div>
</div>