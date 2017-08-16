<style>
    .preview img {
        width: 100%;
        height: 100px;
        margin: 10px;
    }
</style>
<div class="box box-primary">
    <div class="box-header"></div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('content', '消息内容',['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('content', null, [
                    'class' => 'form-control',
                    'placeholder' => '不能超过60个汉字',
                    'data-parsley-required' => 'true',
                    'data-parsley-maxlength' => '60',
                    'data-parsley-minlength' => '2',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('serviceid', '业务id',['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('serviceid', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('message_id', '消息id',['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('message_id', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('url', '页面地址',['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('url', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('media_ids', '图片',['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-6">
                    @if(isset($medias))
                        @foreach($medias as $key => $value)
                            <img src="../../{{$value->path}}">
                            <input type="hidden" name="media_ids[]" value="{{$value->id}}"/>
                        @endforeach
                    @endif
                    <div class="preview" style="width: 100px;overflow: hidden;"></div>
                    <a class="btn btn-primary" data-toggle="modal" data-target="#modalPic">上传</a>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('user_id', '发送者用户',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('user_id', $users, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('user_ids', '接收者用户',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    <select multiple="multiple" name="user_ids[]" id="user_ids">
                        @foreach($users as $key => $value)
                            @if(isset($selectedUsers))
                                <option value="{{$key}}" @if(array_key_exists($key,$selectedUsers))selected="selected"@endif>
                                    {{$value}}
                                </option>
                            @else
                                <option value="{{$key}}">{{$value}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('message_type_id', '消息类型',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('message_type_id', $messageTypes, null, ['class' => 'form-control']) !!}
                </div>
            </div>

        </div>
    </div>
    <div class="box-footer">
        {{--button--}}
        <div class="form-group">
            <div class="col-sm-3 col-sm-offset-2">
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
                    模态框（Modal）标题
                </h4>
            </div>
            <div class="modal-body">
                <input type="file" name="img[]" id="uploadFile" accept="image/jpeg,image/gif,image/png" multiple>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">关闭
                </button>
            </div>
        </div>
    </div>
</div>
