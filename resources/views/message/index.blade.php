<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header')
    </div>
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li>
                    <a href="#tab03" data-toggle="tab">
                        <i class="fa fa-th-large"></i>&nbsp;素材库
                    </a>
                </li>
                <li>
                    <a href="#tab02" data-toggle="tab">
                        <i class="fa fa-archive"></i>&nbsp;已发送
                    </a>
                </li>
                <li class="active">
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
                    @include('message.targets')
                    <div class="overlay" style="display: none; position: fixed; top: 0;left: 0; width: 100%; height: 100%;">
                        <i class="fa fa-refresh fa-spin" style=""></i>
                    </div>
                    <div class="form-horizontal form-main" id="message">
                    {!! Form::open([
                        'method' => 'post',
                        'id' => 'message',
                        'data-parsley-validate' => 'true'
                    ]) !!}
                        <!-- 选择应用 -->
                        @include('partials.multiple_select', [
                            'label' => '应用',
                            'id' => 'app_ids',
                            'icon' => 'fa fa-weixin text-green',
                            'items' => $apps
                        ])
                        <!-- 发送对象 -->
                        <div class="form-group">
                            {!! Form::label('targets', '发送对象', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                <div id="checked-nodes"></div>
                                {!! Form::hidden('selected-node-ids') !!}
                                {!! Form::button('<i class="fa fa-user-plus text-blue">&nbsp;选择</i>', [
                                    'id' => 'choose',
                                    'class' => 'btn btn-box-tool',
                                    'style' => 'margin-top: 3px;'
                                ]) !!}
                            </div>
                        </div>
                        @include('partials.single_select', [
                            'label' => '消息类型',
                            'id' => 'message_type_id',
                            'items' => $messageTypes
                        ])
                        <!-- 消息内容 -->
                        <div class="form-group">
                            {!! Form::label('departmentId', '消息内容', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
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
                                            <a href="#content_voice" data-toggle="tab" class="tab">
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
                                                <i class="fa fa-file-movie-o"></i>&nbsp;文件
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#content_textcard" data-toggle="tab" class="tab">
                                                <i class="fa fa-file"></i>&nbsp;卡片
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
                                            ]) !!}
                                        </div>
                                        <!-- 图片 -->
                                        <div class="tab-pane" id="content_image">
                                            <i class="fa fa-plus text-blue">&nbsp;添加图片</i>
                                            {!! Form::hidden('type') !!}
                                            {!! Form::file('file-image', [
                                                'id' => 'file-image',
                                                'accept' => 'image/*',
                                                /*'class' => 'upload'*/
                                            ]) !!}
                                            {{--<button id="add-image" class="btn btn-box-tool add-btn" type="button">--}}
                                                {{----}}
                                            {{--</button>--}}
                                        </div>
                                        <!-- 语音 -->
                                        <div class="tab-pane" id="content_voice">
                                            <button id="add-voice" class="btn btn-box-tool add-btn" type="button">
                                                <i class="fa fa-plus text-blue">
                                                    &nbsp;添加语音
                                                    {!! Form::hidden('type') !!}
                                                    {!! Form::file('file-voice', [
                                                        'id' => 'file-voice',
                                                        'accept' => 'audio/*',
                                                        /*'class' => 'upload'*/
                                                    ]) !!}
                                                </i>
                                            </button>
                                        </div>
                                        <!-- 视频 -->
                                        <div class="tab-pane" id="content_video">
                                            <span class="text-gray">tips：视频格式支持mp4，大小不能超过10MB</span>
                                            <button id="add-video" class="btn btn-box-tool" type="button">
                                                <i class="fa fa-plus text-blue">&nbsp;添加视频</i>
                                            </button>
                                        </div>
                                        <!-- 文件 -->
                                        <div class="tab-pane" id="content_file">
                                            <button id="add-file" class="btn btn-box-tool" type="button" style="margin-top: 3px; display: block">
                                                <i class="fa fa-plus text-blue">&nbsp;添加文件</i>
                                            </button>
                                        </div>
                                        <!-- 卡片 -->
                                        <div class="tab-pane" id="content_textcard">
                                            <button id="add-textcard" class="btn btn-box-tool" type="button">
                                                <i class="fa fa-plus text-blue">&nbsp;添加卡片</i>
                                            </button>
                                        </div>
                                        <!-- 图文 -->
                                        <div class="tab-pane" id="content_mpnews">
                                            <button id="add-mpnews" class="btn btn-box-tool" type="button"
                                                    style="margin-top: 3px;">
                                                <i class="fa fa-plus text-blue">&nbsp;添加图文</i>
                                            </button>
                                        </div>
                                        <!-- 短信 -->
                                        <div class="tab-pane" id="content_sms">
                                            <input id="content-sms-maxlength" type="hidden" value="{{ $messageMaxSize }}">
                                            {!! Form::textarea('sms-content', null, [
                                                'id' => 'sms-content',
                                                'class' => 'form-control text-blue',
                                            ]) !!}
                                            <span id="content-sms-length" style="color: #777;margin-top: 10px;display: inline-block;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3"></label>
                            <div class="col-sm-6">
                                <input type="button" class="btn btn-default" id="send" value="发送">
                                <input type="button" class="btn btn-default" id="time-send" value="定时发送">
                                <input type="button" class="btn btn-default" id="draft" value="存为草稿">
                                <input type="button" class="btn btn-default" id="preview" value="预览">
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
@include('message.modal_video')

