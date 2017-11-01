    <div class="box">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($message['id']))
                {{ Form::hidden('id', $message['id'], ['id' => 'id']) }}
            @endif
            @include('partials.single_select', [
                    'label' => '通信方式',
                    'id' => 'comm_type_id',
                    'items' => $commtypes
            ])
            @include('partials.single_select', [
                    'label' => '应用',
                    'id' => 'app_id',
                    'items' => $apps
            ])
            @include('partials.single_select', [
                    'label' => '消息类型',
                    'id' => 'message_type_id',
                    'items' => $messageTypes
            ])
            <div class="form-group">
                {!! Form::label('content', '消息内容', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::textarea('content', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过60个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]'
                    ]) !!}
                </div>
            </div>
            {{--<div class="form-group">--}}
                {{--{!! Form::label('serviceid', '业务id',[--}}
                    {{--'class' => 'col-sm-3 control-label'--}}
                {{--]) !!}--}}
                {{--<div class="col-sm-6">--}}
                    {{--{!! Form::text('serviceid', null, [--}}
                        {{--'class' => 'form-control'--}}
                    {{--]) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--{!! Form::label('message_id', '关联消息id', [--}}
                    {{--'class' => 'col-sm-3 control-label'--}}
                {{--]) !!}--}}
                {{--<div class="col-sm-6">--}}
                    {{--{!! Form::text('message_id', null, [--}}
                        {{--'class' => 'form-control'--}}
                    {{--]) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--{!! Form::label('url', '页面地址', [--}}
                    {{--'class' => 'col-sm-3 control-label'--}}
                {{--]) !!}--}}
                {{--<div class="col-sm-6">--}}
                    {{--{!! Form::text('url', null, [--}}
                        {{--'class' => 'form-control'--}}
                    {{--]) !!}--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="form-group">
                {!! Form::label('media_ids', '多媒体', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
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
            {{--@include('partials.single_select', [--}}
            {{--'label' => '发送者用户',--}}
            {{--'id' => 's_user_id',--}}
            {{--'items' => $users--}}
            {{--])--}}
            <div class="form-group">
                {!! Form::label('departmentId', '接收者用户', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div id="department-nodes-checked">
                        @if(isset($selectedDepartments))
                            @foreach($selectedDepartments as $key => $department)
                                <button type="button" class="btn btn-flat" style="margin-right: 5px;margin-bottom: 5px">
                                    <i class="{{$department['icon']}}"></i>
                                    {{$department['text']}}
                                    <i class="fa fa-close close-selected"></i>
                                    <input type="hidden" name="selectedDepartments[]" value="{{$department['id']}}"/>
                                </button>
                            @endforeach

                        @endif
                    </div>
                    @if(isset($selectedDepartmentIds))
                        <input type="hidden" id="selectedDepartmentIds" value="{{$selectedDepartmentIds}}"/>
                    @else
                        <input type="hidden" id="selectedDepartmentIds" value=""/>
                    @endif
                    <a id="add-department" class="btn btn-primary" style="margin-bottom: 5px">修改</a>
                </div>
            </div>
            {{--@include('partials.multiple_select', [--}}
                {{--'label' => '接收者用户',--}}
                {{--'id' => 'r_user_id',--}}
                {{--'items' => $users,--}}
                {{--'selectedItems' => isset($selectedUsers) ? $selectedUsers : []--}}
            {{--])--}}
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
