@extends('wechat.layouts.master')
@section('title') <title>消息中心</title> @endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/wechat/message_center/create.css') }}">
@endsection
@section('content')
    <div class="msg-send-wrap">
        <!-- 发送对象 -->
        <div id="chosen-container" class="scui-chosen js-scui-chosen-container3 js-scui-chosen scui-form-group">
            {!! Form::label(null, '发送对象', ['class' => 'scui-control-label mr4']) !!}
            <div id="chosen-results"></div>
            <span class="icons-chosen chosen-icon js-chosen-icon">
                <a class="icon iconfont icon-add c-green open-popup" href="#" data-target="#targets"></a>
            </span>
        </div>
        <!-- 信息类型 -->
        <div class="weui-cell weui-cell_select weui-cell_select-after" style="background-color: #fff;">
            <div class="weui-cell__hd">
                {!! Form::label('msg-type', '信息类型', ['class' => 'weui-label']) !!}
            </div>
            <div class="weui-cell__bd">
                {!! Form::select('msg-type', $msgTypes, null, [
                    'id' => 'msg-type',
                    'class' => 'weui-input',
                    'disabled' => sizeof($msgTypes) <= 1
                ]) !!}
            </div>
        </div>
        <!-- 消息类型 -->
        <div class="weui-cell weui-cell_select weui-cell_select-after" style="background-color: #fff;">
            <div class="weui-cell__hd">
                {!! Form::label('message_type_id', '消息类型', ['class' => 'weui-label']) !!}
            </div>
            <div class="weui-cell__bd">
                {!! Form::select('message_type_id', $messageTypes, null, [
                    'id' => 'message_type_id',
                    'class' => 'weui-input',
                    'disabled' => sizeof($messageTypes) <= 1
                ]) !!}
            </div>
        </div>
        <div style="height: 5px;"></div>
        <!-- 标题(视频、卡片) -->
        <div id="title-container" class="mt5px msg-send-bg b-bottom hw-title" style="display: none;">
            <div class="weui-cell">
                <div class="weui-cell__bd js-title">
                    {!! Form::text('title', isset($message) ? $message->title : '', [
                        'id' => 'title',
                        'class' => 'weui-input fs18 one-line title',
                        'placeholder' => '标题',
                        'maxlength' => 30
                    ]) !!}
                </div>
            </div>
        </div>
        <!-- 内容(文本、视频、卡片、短信) -->
        <div id="content-container" class="msg-send-conwrap msg-send-bg">
            <div contenteditable="true" id="content" class="wangEditor-mobile-txt">
                {!! isset($message) ? $message->content : ''!!}
            </div>
        </div>
        <!-- 点击后跳转的链接(卡片) -->
        <div id="card-url-container" class="msg-send-bg b-bottom hw-title extra" style="display: none;">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    {!! Form::text('card-url', null, [
                        'id' => 'card-url',
                        'class' => 'weui-input one-line title',
                        'placeholder' => '点击后跳转的地址',
                        'maxlength' => 30
                    ]) !!}
                </div>
            </div>
        </div>
        <!-- 按钮文字(卡片) -->
        <div id="btn-txt-container" class="msg-send-bg b-bottom hw-title extra" style="display: none;">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    {!! Form::text('btn-txt', null, [
                        'id' => 'btn-txt',
                        'class' => 'weui-input one-line title',
                        'placeholder' => '按钮文字。默认为“详情”',
                        'maxlength' => 30
                    ]) !!}
                </div>
            </div>
        </div>
        <div style="height: 5px;"></div>
        <!-- 上传素材(图片、语音、视频、文件) -->
        <div id="upload-container" class="msg-send-conicon msg-send-bg b-top extra" style="display: none;">
            <div class="weui-cell">
                <div class="weui-uploader">
                    <div class="weui-uploader__hd">
                        <p id="upload-title" class="weui-uploader__title"></p>
                    </div>
                    <div class="weui-uploader__bd">
                        <div class="weui-uploader__input-box">
                            {!! Form::hidden('media_id', null, ['id' => 'media_id']) !!}
                            {!! Form::file('upload', [
                                'id' => 'upload',
                                'accept' => '',
                                'class' => 'weui-uploader__input'
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 图文消息 -->
        <div id="mpnews-container" class="msg-send-conicon msg-send-bg b-top" style="display: none;">
            <div class="weui-cell">
                <div class="weui-uploader">
                    <div class="weui-uploader__hd">
                        <p class="weui-uploader__title">添加图文</p>
                    </div>
                    <div class="weui-uploader__bd">
                        <ul class="weui-uploader__files" id="mpnews-list">

                        </ul>
                        <a id="add-mpnews" href="#" class="open-popup weui-uploader__input-box"></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- 定时发送 -->
        <div class="weui-cell weui-cell_switch b-top weui-cells_form mt5px msg-send-bg">
            <div class="weui-cell__bd">定时发送</div>
            <div class="weui-cell__ft">
                <input type="checkbox" title="开启评论" name="openCom" class="weui-switch"></div>
        </div>
        <div class="hw-time b-top" style="display: none;">
            <div class="weui-cell msg-send-bg">
                <div class="weui-cell__hd">
                    <label for="" class="weui-label">发送日期</label>
                </div>
                <div class="weui-cell__bd">
                    <input id="time" name="time" readonly="readonly" type="text" placeholder="请选择日期"
                           class="weui-input ma_expect_date" data-toggle='datetime-picker'>
                </div>
            </div>
        </div>
        <!-- 发送按钮 -->
        <div class="weui-flex mt5px">
            <div class="weui-flex__item">
                <div class="placeholder msg-send-btn" style="padding: 15px;">
                    <a id="send" href="#" class="weui-btn weui-btn_primary">发送</a>
                </div>
            </div>
        </div>
        <!-- 存为草稿 -->
        <div class="weui-flex mt5px">
            <div class="weui-flex__item">
                <div class="placeholder msg-send-btn" style="padding: 15px;">
                    <a id="draft" href="#" class="weui-btn weui-btn_primary">存为草稿</a>
                </div>
            </div>
        </div>
    </div>
    <div id="targets" class='weui-popup__container'>
        <div class="weui-popup__overlay"></div>
        <div class="weui-popup__modal">
            <div class="chosen-container js-scui-chosen-layer">
                <div class="chosen-container-fixed">
                    <div class="chosen-header js-chosen-header weui-cell">
                        <div class="chosen-header-result js-chosen-header-result"></div>
                        <div class="common-left-search">
                            <i class="icon iconfont icon-search3 search-logo icons2x-search"></i>
                            {!! Form::text('search', null, [
                                'id' => 'search',
                                'class' => 'js-search-input',
                                'placeholder' => '搜索'
                            ]) !!}
                        </div>
                    </div>
                    {{--<div class="chosen-breadcrumb js-chosen-breadcrumb">--}}
                        {{--<ol class="breadcrumb js-chosen-breadcrumb-ol">--}}
                            {{--<li data-id="{{ $schoolDept->id }}" class="js-chosen-breadcrumb-li headclick">--}}
                            {{--<a>{{ $schoolDept->name }}</a>--}}
                            {{--</li>--}}
                            {{--<li style="display: none;" id="back">--}}
                                {{--返回部门列表--}}
                                {{--{!! Form::hidden('deptId', null, ['id' => 'deptId']) !!}--}}
                            {{--</li>--}}
                        {{--</ol>--}}
                    {{--</div>--}}
                    <div class="back">
                        <a href="#" class="weui-btn weui-btn_plain-default" id="back" style="display: none;">返回部门列表</a>
                        {!! Form::hidden('deptId', null, ['id' => 'deptId']) !!}
                    </div>
                    {{--<div class="weui-cell" id="back" style="display: none;">--}}
                        {{--<div class="weui-cell_bd">--}}
                            {{----}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="chosen-items js-chosen-items">
                        <div class="weui-cells weui-cells_checkbox" style="padding-bottom: 60px;">
                            <div id="targets-container">
                                {{--年级列表--}}
                                @foreach ($departments as $department)
                                    @include('wechat.message_center.target', [
                                        'type' => 'department',
                                        'target' => $department
                                    ])
                                @endforeach
                                {{--用户列表--}}
                                @foreach ($users as $user)
                                    @include('wechat.message_center.target', [
                                        'type' => 'user',
                                        'target' => $user
                                    ])
                                @endforeach
                            </div>
                        </div>
                        <div style="height: 40px;"></div>
                    </div>
                </div>
                <div class="chosen-footer js-chosen-footer">
                    <div class="weui-cells weui-cells_checkbox">
                        <label class="weui-cell weui-check__label">
                            <div class="weui-cell__hd">
                                {!! Form::checkbox('check-all', 0, null, [
                                    'id' => 'check-all',
                                    'class' => 'weui-check'
                                ]) !!}
                                <i class="weui-icon-checked"></i>
                            </div>
                            <div class="weui-cell__bd">
                                <p>全选</p>
                            </div>
                        </label>
                    </div>
                    <a id="confirm" href="#" class="scui-pull-right weui-btn weui-btn_mini weui-btn_primary close-popup">确定</a>
                    <span id="count">已选0个部门, 0名用户
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div id="mpnews" class="weui-popup__container">
        <div class="weui-popup__overlay"></div>
        <div class="weui-popup__modal" style="background-color: #fff;">
            {!! Form::hidden('mpnews-id', null, ['id' => 'mpnews-id']) !!}
            <!-- 图文标题 -->
            <div class="weui-cell">
                <div class="weui-uploader__hd">
                    {!! Form::label('mpnews-title', '标题', ['class' => 'weui-label']) !!}
                </div>
                <div class="weui-cell__bd js-title">
                    {!! Form::text('mpnews-title', null, [
                        'id' => 'mpnews-title',
                        'class' => 'weui-input fs18 one-line title',
                        'maxlength' => 30
                    ]) !!}
                </div>
            </div>
            <!-- 图文内容 -->
            <div class="weui-cell">
                <div class="weui-cell_bd">
                    {!! Form::textarea('mpnews-content', null, [
                        'id' => 'mpnews-content',
                        'class' => 'weui-textarea',
                        'placeholder' => '(图文内容)',
                        'rows' => 3
                    ]) !!}
                </div>
            </div>
            <!-- 原文链接 -->
            <div class="weui-cell">
                <div class="weui-uploader__hd">
                    {!! Form::label('content-source-url', '链接', [
                        'class' => 'weui-label'
                    ]) !!}
                </div>
                <div class="weui-cell__bd">
                    {!! Form::text('content-source-url', null, [
                        'id' => 'content-source-url',
                        'class' => 'weui-input',
                        'placeholder' => '(原文链接，选填)'
                    ]) !!}
                </div>
            </div>
            <!-- 图文作者 -->
            <div class="weui-cell">
                <div class="weui-uploader__hd">
                    {!! Form::label('author', '作者', [
                        'class' => 'weui-label'
                    ]) !!}
                </div>
                <div class="weui-cell__bd">
                    {!! Form::text('author', null, [
                        'id' => 'author',
                        'class' => 'weui-input',
                        'placeholder' => '(选填)'
                    ]) !!}
                </div>
            </div>
            <!-- 图文摘要 -->
            <div class="weui-cell">
                <div class="weui-uploader__hd">
                    {!! Form::label('digest', '摘要', [
                        'class' => 'weui-label'
                    ]) !!}
                </div>
                <div class="weui-cell__bd">
                    {!! Form::text('digest', null, [
                        'id' => 'digest',
                        'class' => 'weui-input',
                        'placeholder' => '(选填)'
                    ]) !!}
                </div>
            </div>
            <!-- 封面图 -->
            <div class="msg-send-conicon msg-send-bg b-top weui-cell">
                <div class="weui-cell">
                    <div class="weui-uploader">
                        <div class="weui-uploader__hd">
                            <p id="mp-upload-title" class="weui-uploader__title">封面图</p>
                        </div>
                        <div class="weui-uploader__bd">
                            <div class="weui-uploader__input-box">
                                {!! Form::hidden('thumb_media_id', null, ['id' => 'thumb_media_id']) !!}
                                {!! Form::hidden('mp-file-path', null, ['id' => 'mp-file-path']) !!}
                                {!! Form::file('mpnews-upload', [
                                    'id' => 'mpnews-upload',
                                    'accept' => 'image/*',
                                    'class' => 'weui-uploader__input'
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="height: 5px;"></div>
            <div class="weui-cell scui-pull-right">
                <a id="add" href="#" class="btn-mpnews weui-btn weui-btn_mini weui-btn_primary">确定</a>
                <a id="cancel" href="#" class="btn-mpnews weui-btn weui-btn_mini weui-btn_default close-popup">取消</a>
                <a id="delete" href="#" class="btn-mpnews weui-btn weui-btn_mini weui-btn_warn">删除</a>
            </div>
        </div>
    </div>
    <div id="notification">
        <div class="weui-loadmore" style="margin-top: 50%;">
            <i class="weui-loading"></i>
            <span class="weui-loadmore__tips">正在上传</span>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('/js/wechat/message_center/create.js') }}"></script>
@endsection