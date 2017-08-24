<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('id', 'id',['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::hidden('id', null, [
                        'class' => 'form-control',
                    ]) !!}
                </div>
            </div>
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
                {!! Form::label('media_ids', '轮播图',['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-10">
                    <div class="preview">
                        @if(isset($medias))
                            @foreach($medias as $key => $value)
                                <div class="img-item">
                                    <img src="../../../{{$value->path}}" id="{{$value->id}}">
                                    <input type="hidden" name="media_ids[]" value="{{$value->id}}"/>
                                    <div class="del-mask">
                                        <i class="delete fa fa-trash"></i>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <a class="btn btn-primary" data-toggle="modal" data-target="#modalPic">上传</a>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('user_id', '发送者用户',['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('user_id', $users, null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('user_ids', '接收者用户',['class' => 'col-sm-2 control-label']) !!}
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
                {!! Form::label('message_type_id', '消息类型',['class' => 'col-sm-2 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('message_type_id', $messageTypes, null, ['class' => 'form-control']) !!}
                </div>
            </div>

        </div>
    </div>
    @include('partials.form_buttons')
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
