<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header', ['addBtn' => false])
    </div>
    <div class="box-bod">
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
                    @include('message.objects')
                    @include('message.imagetext')
                    @include('message.upload_video')
                    <div class="overlay" style="display: none;position: fixed;top: 0;left: 0;width: 100%;height: 100%;">
					    <i class="fa fa-refresh fa-spin" style=""></i>
					</div>
                    <div class="form-horizontal" id="message">
                    {!! Form::open([
                        'method' => 'post',
                        'id' => 'formImagetext',
                        'data-parsley-validate' => 'true'
                    ]) !!}
                    <style>.select2-search__field{display: none;}</style>
                    <!-- 选择应用 -->
                    @include('partials.multiple_select', [
                        'label' => '应用',
                        'id' => 'app_ids',
                        'icon' => 'fa fa-weixin',
                        'items' => $apps,
                        'selectedItems' => null
                    ])

                    <!-- 发送对象 -->
                        <div class="form-group">
                            {!! Form::label('objects', '发送对象', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                <div id="department-nodes-checked">

                                </div>

                                <input type="hidden" id="selectedDepartmentIds" value=""/>
                                <button id="add-attachment" class="btn btn-box-tool" type="button"
                                        style="margin-top: 3px;">
                                    <i class="fa fa-user-plus text-blue">&nbsp;选择</i>
                                </button>
                            </div>
                        </div>
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
                                            <a href="#content_mpnews" data-toggle="tab" class="tab">
                                                <i class="fa fa-th-list"></i>&nbsp;图文
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#content_image" data-toggle="tab" class="tab">
                                                <i class="fa fa-file-image-o"></i>&nbsp;图片
                                            </a>
                                        </li>
                                        <!--<li>
                                            <a href="#content_voice" data-toggle="tab" class="tab">
                                                <i class="fa fa-file-sound-o"></i>&nbsp;音频
                                            </a>
                                        </li>-->
                                        <li>
                                            <a href="#content_video" data-toggle="tab" class="tab">
                                                <i class="fa fa-file-movie-o"></i>&nbsp;视频
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#content_sms" data-toggle="tab" class="tab">
                                                <i class="fa fa-file-text"></i>&nbsp;短信
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="message-content">
                                        <div class="active tab-pane" id="content_text">
                                            {!! Form::textarea('content', null, [
                                                'id' => 'messageText',
                                                'name' => 'content',
                                                'class' => 'form-control',
                                            ]) !!}
                                        </div>
                                        <div class="tab-pane" id="content_mpnews">
                                            <button id="add-imagetext" class="btn btn-box-tool" type="button"
                                                    style="margin-top: 3px;">
                                                <i class="fa fa-plus text-blue">&nbsp;添加图文</i>
                                            </button>
                                            <!--<div class="show_imagetext" style="width: 270px;border:1px solid #E4E6E9;border-radius: 4px;padding: 10px;position: relative;cursor: pointer;">
                                            	<div class="show_imagetext_title" style="font-size: 16px;line-height: 24px;overflow: hidden;text-overflow:ellipsis;-webkit-line-clamp:2;margin-bottom: 8px;">123</div>
                                            	<div class="show_imagetext_pic" style="height: 125px;width: 250px;background-repeat: no-repeat;background-size:cover;background-image: url({{ URL::asset('img/photo1.png') }});"></div>
                                            	<div class="show_imagetext_content" style="font-size: 12px;margin-top:12px;color:#787878;line-height: 20px;overflow: hidden;text-overflow:ellipsis;-webkit-line-clamp:4;">123123</div>
                                            </div>-->
                                        </div>
                                        <div class="tab-pane" id="content_image">

                                            <form id="uploadImageForm" enctype="multipart/form-data">

                                                <button id="add-image" class="btn btn-box-tool" type="button" style="margin-top: 3px;position: relative;border: 0;">
                                                    <i class="fa fa-plus text-blue">
                                                        &nbsp;添加图片
                                                            <input type="hidden" value="image" name="type" />
                                                            <input type="file" id="file-image" onchange="uploadfile(this)" name="uploadFile" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>
                                                    </i>
                                                </button>
                                            </form>

                                        </div>
                                        <!--<div class="tab-pane" id="content_voice">
                                            <form id="uploadVoiceForm" enctype="multipart/form-data">

                                            <button id="add-voice" class="btn btn-box-tool" type="button" style="margin-top: 3px;position: relative;border: 0;">
                                                <i class="fa fa-plus text-blue">
                                                	&nbsp;添加音频
                                                	<input type="hidden" value="voice" name="type" />
                                                	<input type="file" id="file-voice" onchange="uploadfile(this)" name="uploadFile" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>
                                                </i>
                                            </button>
                                            </form>

                                        </div>-->
                                        <div class="tab-pane" id="content_video">
                                            <span class="text-gray">tips：视频格式支持mp4，大小不能超过10MB</span>
                                        	<button id="add-video" class="btn btn-box-tool" type="button"
                                                    style="margin-top: 3px; display: block">
                                                <i class="fa fa-plus text-blue">&nbsp;添加视频</i>
                                            </button>
                                            <!--<form id="uploadVideoForm" enctype="multipart/form-data">

                                                <button id="add-video" class="btn btn-box-tool" type="button" style="margin-top: 3px;position: relative;border: 0;">
                                                    <i class="fa fa-plus text-blue">
                                                        &nbsp;添加视频
                                                        <input type="hidden" value="video" name="type" />
                                                        <input type="file" id="file-video" onchange="uploadfile(this)" name="uploadFile" accept="video/mp4"  style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>
                                                    </i>
                                                </button>
                                            </form>-->
                                        </div>
                                        <div class="tab-pane" id="content_sms">
                                        	<input id="content-sms-maxlength" type="hidden" value="{{$messageMaxSize}}" >
                                            {!! Form::textarea('content', null, [
                                                'id' => 'contentSms',
                                                'class' => 'form-control',
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
                                {{--<input type="button" class="btn btn-default" id="time-send" value="定时发送">--}}
                                {{--<input type="button" class="btn btn-default" id="draft" value="存为草稿">--}}
                                {{--<input type="button" class="btn btn-default" id="preview" value="预览">--}}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                <style>
                	.dataTables_scrollHeadInner{width: 100% !important;}
                	.table-striped.table-bordered{width: 100% !important;}
                </style>
                <div class="tab-pane" id="tab02">
                    <table id="data-table" style="width: 100%"
                           class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr class="bg-info">
                            <th>#</th>
                            <th>通信方式</th>
                            <th>应用</th>
                            <th>消息批次</th>
                            <th>接收者</th>
                            <th>类型</th>
                            <th>已读</th>
                            <th>已发</th>
                            <th>创建于</th>
                            <th>更新于</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="tab-pane" id="tab03">
                </div>
            </div>
        </div>
    </div>
</div>

