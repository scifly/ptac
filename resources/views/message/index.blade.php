@include('partials.modal_delete')
@include('partials.modal_show')
<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header')
    </div>
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="action-type">
                    <a href="#tab03" data-toggle="tab">
                        <i class="fa fa-th-large"></i>&nbsp;素材库
                    </a>
                </li>
                <li class="action-type">
                    <a href="#tab02" data-toggle="tab">
                        <i class="fa fa-history"></i>&nbsp;已发送
                    </a>
                </li>
                <li class="active action-type">
                    <a href="#tab01" data-toggle="tab">
                        <i class="fa fa-send"></i>&nbsp;发消息
                    </a>
                </li>
                <li class="pull-left header">
                    <i class="fa fa-send"></i>消息中心
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab01">
                    @include('partials.tree', [
                        'title' => '发送对象',
                        'selectedTitle' => '已选择的发送对象'
                    ])
                    <div class="upload-overlay overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <div class="form-horizontal" id="message">
                        {!! Form::open([
                            'method' => 'post',
                            'id' => 'formMessage',
                            'data-parsley-validate' => 'true'
                        ]) !!}
                        <!-- 选择应用 -->
                        @include('partials.single_select', [
                            'label' => '应用',
                            'id' => 'app_ids',
                            'icon' => 'fa fa-weixin text-green',
                            'items' => $apps,
                        ])
                        {!! Form::hidden('id', null, ['id' => 'id']) !!}
                        <!-- 发送对象 -->
                        <div class="form-group">
                            {!! Form::label('targets', '发送对象', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                <div id="checked-nodes"></div>
                                {!! Form::hidden('selected-node-ids', null, [
                                    'id' => 'selected-node-ids',
                                ]) !!}
                                {!! Form::button('<i class="fa fa-user-plus text-blue">&nbsp;选择</i>', [
                                    'id' => 'choose',
                                    'class' => 'btn btn-box-tool',
                                    'style' => 'margin-top: 3px;'
                                ]) !!}
                            </div>
                        </div>
                        <!-- 消息类型 -->
                        @include('partials.single_select', [
                            'label' => '消息类型',
                            'id' => 'message_type_id',
                            'items' => $messageTypes
                        ])
                        <!-- 消息内容 -->
                        <div class="form-group">
                            {!! Form::label('content', '消息内容', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs" id="message-format">
                                        <li class="active">
                                            <a href="#content_text" data-toggle="tab" class="tab">
                                                <i class="fa fa-file-text-o"></i>&nbsp;文本
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#content_image" data-toggle="tab" class="tab">
                                                <i class="fa fa-file-image-o"></i>&nbsp;图片
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#content_audio" data-toggle="tab" class="tab">
                                                <i class="fa fa-file-sound-o"></i>&nbsp;语音
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#content_video" data-toggle="tab" class="tab">
                                                <i class="fa fa-file-movie-o"></i>&nbsp;视频
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#content_file" data-toggle="tab" class="tab">
                                                <i class="fa fa-file-o"></i>&nbsp;文件
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#content_card" data-toggle="tab" class="tab">
                                                <i class="fa fa-folder-o"></i>&nbsp;卡片
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#content_mpnews" data-toggle="tab" class="tab">
                                                <i class="fa fa-th-list"></i>&nbsp;图文
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#content_sms" data-toggle="tab" class="tab">
                                                <i class="fa fa-file-text"></i>&nbsp;短信
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="message-content">
                                        <!-- 文本 -->
                                        <div class="active tab-pane" id="content_text">
                                            {!! Form::textarea('text-content', null, [
                                                'id' => 'text-content',
                                                'placeholder' => '（请在此输入文本消息内容）',
                                                'class' => 'form-control text-blue',
                                                'title' => '消息内容',
                                                'rows' => 5,
                                                'required' => 'true'
                                            ]) !!}
                                        </div>
                                        <!-- 图片 -->
                                        <div class="tab-pane" id="content_image">
                                            @include('message.file_upload', [
                                                'id' => 'file-image',
                                                'label' => '上传图片',
                                                'accept' => 'image/*',
                                                'note' => '支持jpg, png两种格式，大小不得超过2M'
                                            ])
                                        </div>
                                        <!-- 语音 -->
                                        <div class="tab-pane" id="content_audio">
                                            @include('message.file_upload', [
                                                'id' => 'file-audio',
                                                'label' => '上传语音',
                                                'accept' => 'audio/*',
                                                'note' => '仅支持amr格式，大小不得超过2M，时长不得超过60秒'
                                            ])
                                        </div>
                                        <!-- 视频 -->
                                        <div class="tab-pane" id="content_video">
                                            {!! Form::text('video-title', null, [
                                                'id' => 'video-title',
                                                'class' => 'form-control text-blue',
                                                'placeholder' => '请在此输入视频标题',
                                                'style' => 'margin-bottom: 5px;'
                                            ]) !!}
                                            {!! Form::textarea('video-description', null, [
                                                'id' => 'video-description',
                                                'class' => 'form-control text-blue',
                                                'placeholder' => '请在此添加视频描述(选填)',
                                                'rows' => 5,
                                                'style' => 'margin-bottom: 5px;'
                                            ]) !!}
                                            <div id="video-container">
                                                @include('message.file_upload', [
                                                    'id' => 'file-video',
                                                    'label' => '上传视频',
                                                    'accept' => 'video/mp4',
                                                    'note' => '支持mp4格式，大小不得超过10M'
                                                ])
                                            </div>
                                        </div>
                                        <!-- 文件 -->
                                        <div class="tab-pane" id="content_file">
                                            @include('message.file_upload', [
                                                'id' => 'file-file',
                                                'label' => '上传文件',
                                                'note' => '大小不得超过20M'
                                            ])
                                        </div>
                                        <!-- 卡片 -->
                                        <div class="tab-pane" id="content_card">
                                            {!! Form::text('card-title', null, [
                                                'id' => 'card-title',
                                                'class' => 'form-control text-blue',
                                                'placeholder' => '请在此输入卡片标题',
                                                'title' => '卡片标题',
                                                'style' => 'margin-bottom: 5px;'
                                            ]) !!}
                                            {!! Form::textarea('card-description', null, [
                                                'id' => 'card-description',
                                                'class' => 'form-control text-blue',
                                                'placeholder' => '请在此添加卡片描述',
                                                'title' => '卡片描述',
                                                'rows' => 5,
                                                'style' => 'margin-bottom: 5px;'
                                            ]) !!}
                                            {!! Form::text('card-url', null, [
                                                'id' => 'card-url',
                                                'class' => 'form-control text-blue',
                                                'placeholder' => '请在此输入卡片链接地址',
                                                'title' => '链接地址',
                                                'style' => 'margin-bottom: 5px;'
                                            ]) !!}
                                            {!! Form::text('card-btntxt', '详情', [
                                                'id' => 'card-btntxt',
                                                'class' => 'form-control text-blue',
                                                'title' => '卡片按钮名称'
                                            ]) !!}
                                        </div>
                                        <!-- 图文 -->
                                        <div class="tab-pane" id="content_mpnews">
                                            <a id="add-mpnews" class="btn btn-box-tool" type="button"
                                               style="margin-top: 3px;">
                                                <i class="fa fa-plus text-blue">&nbsp;添加图文</i>
                                            </a>
                                        </div>
                                        <!-- 短信 -->
                                        <div class="tab-pane" id="content_sms">
                                            {!! Form::hidden('sms-maxlength', $smsMaxLength, [
                                                'id' => 'sms-maxlength',
                                            ]) !!}
                                            {!! Form::textarea('sms-content', null, [
                                                'id' => 'sms-content',
                                                'rows' => 5,
                                                'class' => 'form-control text-blue',
                                            ]) !!}
                                            <p id="sms-length" class="help-block"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @include('partials.enabled', [
                            'id' => 'schedule',
                            'label' => '发送时间',
                            'value' => 0,
                            'options' => ['定时', '立即']
                        ])
                        <!-- 定时发送 -->
                        <div class="form-group" id="timing" style="display: none;">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    @include('partials.icon_addon', ['class' => 'fa-clock-o'])
                                    {!! Form::text('time', null, [
                                        'id' => 'time',
                                        'title' => '发送时间',
                                        'class' => 'form-control'
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3"></label>
                            <div class="col-sm-6">
                                <button class="btn btn-primary btn-sm margin" id="send">
                                    <i class="fa fa-send-o"> 发送</i>
                                </button>
                                <button class="btn btn-success btn-sm margin" id="preview">
                                    <i class="fa fa-play-circle"> 预览</i>
                                </button>
                                <button class="btn btn-default btn-sm margin" id="draft">
                                    <i class="fa fa-save"> 存为草稿</i>
                                </button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                <div class="tab-pane" id="tab02">
                    <table id="data-table" style="width: 100%"
                           class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr class="bg-info">
                            @foreach ($titles as $title)
                                <th>{!! $title !!}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="tab-pane" id="tab03"></div>
            </div>
        </div>
    </div>
</div>
@include('message.modal_mpnews')
