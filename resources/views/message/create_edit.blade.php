<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($message['id']))
                {{ Form::hidden('id', $message['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('content', '消息内容', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('content', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过60个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('serviceid', '业务id',[
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('serviceid', null, [
                        'class' => 'form-control'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('message_id', '消息id', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('message_id', null, [
                        'class' => 'form-control'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('url', '页面地址', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('url', null, [
                        'class' => 'form-control'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('media_ids', '轮播图', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-10">
                    <div class="preview">
                        @if(isset($medias))
                            @foreach($medias as $key => $value)
                                @if(!empty($value))

                                    <div class="img-item">
                                        <img src="../../{{$value->path}}" id="{{$value->id}}">
                                        <input type="hidden" name="media_ids[]" value="{{$value->id}}"/>
                                        <div class="del-mask">
                                            <i class="delete fa fa-trash"></i>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <a class="btn btn-primary" data-toggle="modal" data-target="#modalPic">上传</a>
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '发送者用户',
                'id' => 'user_id',
                'items' => $users
            ])
            @include('partials.multiple_select', [
                'label' => '接收者用户',
                'id' => 'user_ids',
                'items' => $users,
                'selectedItems' => isset($selectedUsers) ? $selectedUsers : []
            ])
            @include('partials.single_select', [
                'label' => '消息类型',
                'id' => 'message_type_id',
                'items' => $messageTypes
            ])
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
