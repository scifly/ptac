//# sourceURL=index.js
// 初始化select2
page.initSelect2([{
    option: {
        templateResult: page.formatStateImg,
        templateSelection: page.formatStateImg
    },
    id: 'app_ids'
}]);
// 初始化"已发送"datatable
page.initDatatable('messages', [
    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6]}
]);
// 加载消息中心css
page.loadCss('css/message/message.css');

var title,
    $token = $('#csrf_token'),
    $message = $('#message'),

    // 发送对象
    $targets = $('#targets'),
    $choose = $('#choose'),

    // 图文
    $imageText = $('#modal-imagetext'),
    $addImageText = $('#add-imagetext'),
    $saveImageText = $('#save-imagetext'),
    $insertUrl = $('#insert-url'),
    $coverImage = $('#cover-image'),

    // 视频
    $video = $('#modal_video'),
    $addVideo = $('#add-video'),
    $saveVideo = $('#save-video'),

    // 短信
    smsMaxlength = $('#content-sms-maxlength').val(),
    $contentSmsLength = $('#content-sms-length'),
    $contentSms = $('.tab-pane.active #content-sms'),
    currentLength = '',
    availableLength = '',

    // 发送按钮
    $send = $('#send');

/** 发送对象 ---------------------------------------------------------------------------------------------------------- */
// 选择发送对象
$choose.on('click', function () {
    $message.hide();
    $targets.show();
});
// 部门及联系人树加载
$.getMultiScripts(['js/tree.js']).done(function () {
    $.tree().list('messages/index', 'contact');
});
// 关闭发送对象选择窗口
$(document).on('click', '#cancel .close-targets', function () {
    $message.show();
    $targets.hide();
});

/** 图文 ------------------------------------------------------------------------------------------------------------- */
// 添加图文
$addImageText.on('click', function () {
    $('#modal-imagetext').modal({backdrop: true});
});
// 添加图文 - 插入原文链接
$insertUrl.on('click', function () {
    $(this).next().slideToggle('fast');
    $(this).next().val('');
});
// 添加图文 - 保存图文
$saveImageText.on('click', function () {
    var $imageTextTitle = $('.imagetext-title'),
        $cover = $('#cover'),
        title = $imageTextTitle.val();
    if (title === '') {
        page.inform(title, '请输入标题', page.info);
        return false;
    }
    var content = $imageTextTitle.val();
    if (content === '') {
        page.inform(title, '请编辑内容', page.info);
        return false;
    }
    var picurl = $cover.find('.show').find('.show-cover');
    if (!picurl) {
        page.inform(title, '请添加封面图', page.info);
        return false;
    } else {
        picurl = picurl.replace('url("', '').replace('")', '');
        var picid = $cover.find('.show-cover input').eq(0).val();
        var picMediaId = $cover.find('.show-cover input').eq(1).val();
    }

    var content_source_url = $('.imagetext-content_source_url').val();
    var author = $('.imagetext-author').val();
    var html =
        '<div class="show_imagetext">' +
        '<div class="show_imagetext_title">' + title + '</div>' +
        '<div class="show_imagetext_pic' + picurl + '"></div>' +
        '<div class="show_imagetext_content">' + content + '</div>' +
        '<input type="hidden" class="show_imagetext_pic_media_id" value="' + picid + '">' +
        '<input type="hidden" class="show_imagetext_media_id" value="' + picMediaId + '">' +
        '<input type="hidden" class="show_imagetext_author" value="' + author + '">' +
        '<input type="hidden" class="show_imagetext_content_source_url" value="' + content_source_url + '">' +
        '</div>';
    $('.tab-pane.active#content_mpnews').html(html);
    $message.show();
    $imageText.hide();
    showImageText();
});

/** 视频 ------------------------------------------------------------------------------------------------------------- */
// 添加视频
$addVideo.on('click', function () {
    $('#modal-video').modal({ backdrop: true});
});
// 保存视频
$saveVideo.on('click', function () {
    var title = $('.video-title').val(),
        $fileVideo = $('#filevideo'),
        videoId = null,
        videoUrl = $fileVideo.find('video').attr("src");

    if (title === '') {
        page.inform('添加视频', '请输入标题', page.failure);
        return false;
    }
    if (!videoUrl) {
        page.inform('添加视频', '请上传视频', page.failure);
        return false;
    } else {
        videoId = $fileVideo.find('.changefile input').eq(0).val();
    }
    var description = $('.imagetext-description').val();
    var html =
        '<div class="showVideo">' +
        '<div class="show_video_title">' + title + '</div>' +
        '<video controls="controls" class="show_video_main" src="' + videoUrl + '"></video>' +
        '<div class="show_video_description">' + description + '</div>' +
        '<input type="hidden" class="show_video_media_id" value="' + videoId + '">' +
        '</div>';
    $('.tab-pane.active#content_video').html(html);
    $message.show();
    $fileVideo.find('video')[0].pause();
    showVideo();
});

/** 短信 ------------------------------------------------------------------------------------------------------------- */
// 获取短信输入字符数
$contentSmsLength.text('已输入0个字符， 还可输入' + smsMaxlength + '个字符');
$contentSms.attr('maxlength', smsMaxlength);
$contentSms.bind("input propertychange", function () {
    currentLength = $(this).val().length;
    availableLength = smsMaxlength - currentLength;
    $contentSmsLength.text('已输入' + currentLength + '个字符， 还可输入' + availableLength + '个字符');
});

/** 发送消息 ---------------------------------------------------------------------------------------------------------- */
$send.on('click', function () {
    var appIds = $('#app_ids').val();
    var selectedDepartmentIds = $('#selectedDepartmentIds').val();
    var type = $('#message-content').find('.tab-pane.active').attr('id');
    type = type.substring('8');
    var content = '';
    var media_id = '';
    switch (type) {
        case 'text': // 文本
            content = {text: $('#messageText').val()};
            break;
        case 'mpnews': // 图文
            var articles = {
                title: $('.show_imagetext_title').text(),
                content: $('.show_imagetext_content').html(),
                author: $('.show_imagetext_author').val(),
                content_source_url: $('.show_imagetext_content_source_url').val(),
                thumb_media_id: $('.show_imagetext_pic_media_id').val(),
            };
            content = {articles: articles};
            media_id = $('.show_imagetext_media_id').val();
            break;
        case 'image': // 图片
            content = {media_id: $('#image_media_id').val()};
            media_id = $('#image-media-id').val();
            break;
        case 'voice': // 音频
            content = {media_id: $('#voice_media_id').val()};
            media_id = $('#voice-media-id').val();
            break;
        case 'video': // 视频
            var video = {
                media_id: $('#video_media_id').val(),
                title: $('.show_video_title').text(),
                description: $('.show_video_description').text(),
            };
            content = {video: video};
            media_id = $('#video-media-id').val();
            break;
        case 'sms': // 短信
            content = {sms: $('#contentSms').val()};
            break;
        default:
            break;
    }

    if (appIds.toString() === '') {
        page.inform(title, '应用不能为空', page.failure);
        return false
    }
    if (selectedDepartmentIds === '') {
        page.inform(title, '对象不能为空', page.failure);
        return false
    }
    if (content['text'] === '') {
        page.inform(title, '内容不能为空', page.failure);
        return false
    }
    if (content['sms'] === '') {
        page.inform(title, '短信内容不能为空', page.failure);
        return false
    }
    $.ajax({
        url: page.siteRoot() + "messages/store",
        type: 'POST',
        dataType: 'json',
        data: {
            app_ids: appIds,
            departIds: selectedDepartmentIds,
            type: type,
            content: content,
            media_id: media_id,
            _token: $token.attr('content')
        },
        success: function (result) {
            page.inform(result.title, result.message, page.success);
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
});

/** Helper functions ------------------------------------------------------------------------------------------------ */
// 上传文件
function upload(obj) {
    var $this = $(obj);
    var type = $this.prev().val(),
        $fileType = $(`#file-${type}`);
    var extension = $fileType[0].files[0].name.split('.');
    extension = extension[extension.length - 1];
    extension = extension.toUpperCase();
    switch (type) {
        case 'image':
            if (extension !== 'JPG' && extension !== 'PNG') {
                alert('请上传JPG或PNG格式的图片');
                return false;
            }
            break;
        case 'voice':// 上传语音文件仅支持AMR格式
            if (extension !== 'AMR') {
                alert('请上传AMR格式的文件');
                return false;
            }
            break;
        case 'video':// 上传视频文件仅支持MP4格式
            if (extension !== 'MP4') {
                alert('请上传MP4格式的视频');
                return false;
            } else {
                if ($('#file-' + type)[0].files[0].size > 10485760) {
                    alert('请上传10MB以内的视频');
                    return false;
                }
            }
            break;
    }

    page.inform(title, '文件上传中...', page.info);
    var formData = new FormData();

    formData.append('uploadFile', $fileType[0].files[0]);
    formData.append('_token', $token.attr('content'));
    formData.append('type', type);
    var $messageContent = $('#message-content');
    $('.overlay').show();
    //请求接口
    $.ajax({
        url: page.siteRoot() + "messages/uploadFile",
        type: 'POST',
        cache: false,
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            $('.overlay').hide();
            if (result.statusCode) {
                page.inform(result.title, result.message, page.success);
                var html = '<form id="uploadForm" enctype="multipart/form-data">';
                switch (type) {
                    case 'image':
                        //图片
                        html +=
                            '<div class="fileshow" style="display: inline-block;width: auto;position: relative;">' +
                            '<img src="../../' + result.data.path + '" style="height: 200px;">' +
                            '<input id="image_media_id" type="hidden" value="' + result.data.media_id + '"/>' +
                            '<input id="image-media-id" type="hidden" value="' + result.data.id + '"/>' +
                            '<input type="hidden" value="image" name="type" />' +
                            '<input type="file" id="file-image" onchange="upload(this)" name="uploadFile" accept="image/*"/>' +
                            '<i class="fa fa-close file-del"></i>' +
                            '</div>' +
                            '</form>';
                        $messageContent.find('.tab-pane.active').html(html);
                        removeFile(type);
                        break;
                    case 'voice':
                        //音频
                        html +=
                            '<div class="fileshow">' +
                            '<i class="fa fa-file-sound-o">' +
                            '<span id="voice">' + result.data.filename + '' +
                            '<input  id="voice_media_id"  type="hidden" value="' + result.data.media_id + '"/>' +
                            '<input  id="voice-media-id"  type="hidden" value="' + result.id + '"/>' +
                            '<input type="hidden" value="voice" name="type" />' +
                            '<input id="file-voice" type="file" onchange="upload(this)" name="uploadFile"/>' +
                            '</span>' +
                            '<i class="fa fa-close file-del" id="close-voice"></i>' +
                            '</div>' +
                            '</form>';
                        $messageContent.find('.tab-pane.active').html(html);
                        removeFile(type);
                        break;
                    case 'video':
                        //视频
                        html +=
                            '<video src="../../' + result.data.path + '" controls="controls" style="height:180px"></video>' +
                            '<div class="btns">' +
                            '<a class="changefile" style="position: relative;margin-left: 10px;">更改' +
                            '<input  id="video_media_id"  type="hidden" value="' + result.data.media_id + '"/>' +
                            '<input  id="video-media-id"  type="hidden" value="' + result.data.id + '"/>' +
                            '<input type="hidden" value="video" name="type" />' +
                            '<input type="file" id="file-video" onchange="upload(this)" name="uploadFile" accept="video/mp4"/>' +
                            '</a>' +
                            '<a class="delfile file-del video-del">删除</a>' +
                            '</form>';
                        $('#filevideo').html(html);
                        removeVideo();
                        break;
                }

            } else {
                page.inform(result.title, result.message, page.failure);
            }
        }
    })
}

// 移除文件
function removeFile(type) {
    $('.tab-pane.active .file-del').click(function () {
        var btntxt = '';
        var fileaccept = '';
        switch (type) {
            case 'image':
                btntxt = '添加图片';
                fileaccept = 'image/*';
                break;
            case 'voice':
                btntxt = '添加音频';
                fileaccept = '';
                break;
            case 'video':
                btntxt = '添加视频';
                fileaccept = 'video/mp4';
                break;
        }
        var html =
            '<form id="uploadForm" enctype="multipart/form-data">' +
            '<button id="add-' + type + '" class="btn btn-box-tool" type="button" style="margin-top: 3px;position: relative;border: 0;">' +
            '<i class="fa fa-plus text-blue">' +
            '&nbsp;' + btntxt + '' +
            '<input type="hidden" value="' + type + '" name="type" />' +
            '<input type="file" id="file-' + type + '" onchange="upload(this)" name="uploadFile" accept="' + fileaccept + '" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>' +
            '</i>' +
            '</button>' +
            '</form>';
        $('#message-content').find('.tab-pane.active').html(html);
    });
}

// 上传图文消息封面图
function uploadCover() {
    var extension = $coverImage[0].files[0].name.split('.');

    extension = extension[extension.length - 1].toUpperCase();
    if (extension !== 'JPG' && extension !== 'PNG') {
        page.inform(title, '请上传JPG或PNG格式的图片', page.info);
        return false;
    }
    page.inform(title, '图片上传中...', page.info);

    $('.overlay').show();
    //请求接口
    $.ajax({
        url: page.siteRoot() + "messages/uploadFile",
        type: 'POST',
        data: {
            _token: $token.attr('content'),
            uploadFile: $coverImage[0].files[0],
            type: 'image'
        },
        success: function (result) {
            $('.overlay').hide();
            page.inform(result.title, result.message, page.success);
            var html =
                '<form id="uploadForm" enctype="multipart/form-data">' +
                    '<div class="show-cover" style="position: relative; height: 130px; width: 130px; background-image: url(../../' + result.data.path + '); background-size: cover;">' +
                        '<input type="hidden" value="' + result.data.media_id + '" name="media_id" />' +
                        '<input type="hidden" value="' + result.data.id + '" name="news-media-id" />' +
                        '<input type="hidden" value="image" name="type" />' +
                        '<input type="file" id="file-cover" onchange="uploadCover(this)" name="input-cover" accept="image/*"/>' +
                        '<i class="fa fa-close cover-del" id="cover"></i>' +
                    '</div>' +
                '</form>';
            $('#cover').html(html);
            removeCover();
        },
        error: function (e) {
            page.errorHandler(e);
        }
    })
}

// 移除图文消息封面图
function removeCover() {
    $('.cover-del').on('click', function () {
        var html =
            '<form id="form-cover" enctype="multipart/form-data">' +
                '<a href="#" style="position: relative;">' +
                    '添加封面图' +
                    '<input type="hidden" value="image" name="type" />' +
                    '<input type="file" id="file-cover" onchange="uploadCover(this)" name="input-cover" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>' +
                '</a>' +
                '&nbsp;&nbsp;<span class="text-gray">建议尺寸:1068*534</span>' +
            '</form>';
        $('#cover').html(html);
    });
}

// 显示图文消息
function showImageText() {
    $('.show_imagetext').click(function () {
        $message.hide();
        $imageText.show();
    })
}

// 移除视频
function removeVideo() {
    $('.video-del').on('click', function () {
        var html =
            '<form id="form-cover" enctype="multipart/form-data">' +
                '<a href="#" style="position: relative;">' +
                    '添加视频' +
                    '<input type="hidden" value="video" name="type" />' +
                    '<input type="file" id="file-video" onchange="upload(this)" name="input-video" accept="video/mp4" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>' +
                '</a>' +
                '&nbsp;&nbsp;<span class="text-gray">(支持MP4)</span>' +
            '</form>';

        $('#filevideo').html(html);
    });

}

// 显示视频
function showVideo() {
    $('.showVideo').click(function () {
        $message.hide();
        $video.show();
        $('.tab-pane.active#content_video video')[0].pause();
    })
}