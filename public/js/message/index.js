//# sourceURL=index.js
var title,
    token = $('#csrf_token').attr('content'),
    $message = $('#message'),
    $messageContent = $('#message-content'),

    // 发送对象
    $targets = $('#targets'),
    $choose = $('#choose'),

    // 文本
    $textContent = $('#text-content'),

    // 图片

    // 语音

    // 视频
    $videoTitle = $('#video-title'),
    $videoDescription = $('#video-description'),

    // 文件

    // 卡片
    $textcardTitle = $('#textcard-title'),
    $textcardDescription = $('#textcard-description'),
    $textcardUrl = $('#textcard-url'),
    $textcardBtntxt = $('#textcard-btntxt'),

    // 图文
    $addMpnews = $('#add-mpnews'),
    $modalMpnews = $('#modal-mpnews'),
    $mpnewsTitle = $('#mpnews-title'),
    $mpnewsContent = $('#mpnews-content'),
    $fileMpnewsImage = $('#file-mpnews-image'),
    $contentSourceUrl = $('#content-source-url'),
    $mpnewsAutho = $('#mpnews-author'),
    $saveMpnews = $('#save-mpnews'),
    $coverImage = $('#cover-image'),

    // 短信
    smsMaxlength = $('#content-sms-maxlength').val(),
    $contentSmsLength = $('#content-sms-length'),
    $contentSms = $('.tab-pane.active #content-sms'),
    currentLength = '',
    availableLength = '',

    // 发送按钮
    $send = $('#send');

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

// 初始化上传文件的事件
$(document).on('change', '.file-upload', function () { upload($(this))});
// 初始化移除上传文件的事件
$(document).on('click', '.tab-pane.active .file-del', function () {
    var btntxt = '';
    var fileaccept = '';
    switch (type) {
        case 'image':
            btntxt = '上传图片';
            fileaccept = 'image/*';
            break;
        case 'voice':
            btntxt = '上传语音';
            fileaccept = 'audio/*';
            break;
        case 'video':
            btntxt = '上传视频';
            fileaccept = 'video/mp4';
            break;
        case 'file':
            btntxt = '上传文件';
    }
    var html =
        '<label for="file-' + type + '"' + ' class="custom-file-upload">' +
        '<i class="fa fa-cloud-upload"></i>' + btntxt +
        '</label>' +
        '<input id="file-' + type + '" accept="' + fileaccept + '/*" type="file">';
    $messageContent.find('.tab-pane.active').html(html);
});
/** 图片 ------------------------------------------------------------------------------------------------------------- */

/** 语音 ------------------------------------------------------------------------------------------------------------- */

/** 视频 ------------------------------------------------------------------------------------------------------------- */

/** 文件 ------------------------------------------------------------------------------------------------------------- */

/** 图文 ------------------------------------------------------------------------------------------------------------- */
// 添加图文
$addMpnews.on('click', function () { $modalMpnews.modal({backdrop: true}); });
// 保存图文
$saveMpnews.on('click', function () {
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
    $modalMpnews.hide();
    // 初始化图文显示事件
    $('.show_imagetext').click(function () {
        $message.hide();
        $modalMpnews.show();
    });
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
    var targetIds = $('#selectedDepartmentIds').val();
    var type = $('#message-content').find('.tab-pane.active').attr('id');
    type = type.substring('8');
    var content = '';
    var mediaId = '';
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
            mediaId = $('.show_imagetext_media_id').val();
            break;
        case 'image': // 图片
            content = {media_id: $('#image_media_id').val()};
            mediaId = $('#image-media-id').val();
            break;
        case 'voice': // 音频
            content = {media_id: $('#voice_media_id').val()};
            mediaId = $('#voice-media-id').val();
            break;
        case 'video': // 视频
            var video = {
                media_id: $('#video_media_id').val(),
                title: $('.show_video_title').text(),
                description: $('.show_video_description').text(),
            };
            content = {video: video};
            mediaId = $('#video-media-id').val();
            break;
        case 'sms': // 短信
            appIds = [0];
            content = {sms: $('#contentSms').val()};
            break;
        default:
            break;
    }

    if (appIds.toString() === '') {
        page.inform(title, '应用不能为空', page.failure);
        return false
    }
    if (targetIds === '') {
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
            appIds: appIds,
            targetIds: targetIds,
            messageTypeId: $('#message_type_id').val(),
            type: type,
            content: content,
            _token: token
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
function upload($file) {
    var file = $file[0].files[0],
        types = $file.attr('id').split('-'),
        type = types[types.length - 1],
        names = file.name.split('.'),
        ext = names[names.length - 1].toUpperCase();

    switch (type) {
        case 'image':
            if (ext !== 'JPG' && ext !== 'PNG') {
                return warning('请上传JPG或PNG格式的图片');
            }
            break;
        case 'voice':// 上传语音文件仅支持AMR格式
            if (ext !== 'AMR') {
                return warning('请上传AMR格式的文件');
            }
            break;
        case 'video':// 上传视频文件仅支持MP4格式
            if (ext !== 'MP4') {
                return warning('请上传MP4格式的视频');
            } else {
                if ($file.size > 10485760) {
                    return warning('请上传10MB以内的视频');
                }
            }
            break;
        default:
            return false;
    }

    page.inform(title, '文件上传中...', page.info);
    $('.overlay').show();
    var data = new FormData();
    data.append('file', file);
    data.append('_token', token);
    data.append('type', type);
    //请求接口
    $.ajax({
        type: 'POST',
        url: page.siteRoot() + "messages/index",
        data: data,
        contentType: false,
        processData: false,
        success: function (result) {
            $('.overlay').hide();
            page.inform(result.title, result.message, page.success);
            var html = '<form id="uploadForm" enctype="multipart/form-data">';
            switch (type) {
                case 'image':
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
                    // 初始化移除视频事件
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
                    break;
                default:
                    return false;
            }
        },
        error: function (e) {
            page.errorHandler(e);
        }
    })
}

// 移除文件
function removeFile(type) {

}

// 上传图文消息封面图
function uploadCover() {
    var ext = $coverImage[0].files[0].name.split('.');

    ext = ext[ext.length - 1].toUpperCase();
    if (ext !== 'JPG' && ext !== 'PNG') {
        page.inform(title, '请上传JPG或PNG格式的图片', page.info);
        return false;
    }
    page.inform(title, '图片上传中...', page.info);

    $('.overlay').show();
    //请求接口
    $.ajax({
        url: page.siteRoot() + "messages/index",
        type: 'POST',
        data: {
            _token: token,
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

function warning(message) {
    page.inform('上传文件', message, page.failure);
    return false;
}