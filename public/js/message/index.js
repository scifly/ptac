//# sourceURL=index.js
var token = $('#csrf_token').attr('content'),
    $message = $('#message'),
    $messageTypeId = $('#message_type_id'),
    $messageContent = $('#message-content'),

    // 发送对象
    $targets = $('#targets'),
    $choose = $('#choose'),

    // 文本
    $textContent = $('#text-content'),

    // 视频
    $videoTitle = $('#video-title'),
    $videoDescription = $('#video-description'),

    // 卡片
    $cardTitle = $('#card-title'),
    $cardDescription = $('#card-description'),
    $cardUrl = $('#card-url'),
    $cardBtntxt = $('#card-btntxt'),

    // 图文
    $contentMpnews = $('#content_mpnews'),
    $formMpnews = $('#formMpnews'),
    $addMpnews = $('#add-mpnews'),
    $mpnewsId = $('#mpnews-id'),
    $mpnewsTitle = $('#mpnews-title'),
    $mpnewsContent = $('#mpnews-content'),
    $contentSourceUrl = $('#content-source-url'),
    $mpnewsDigest = $('#mpnews-digest'),
    $mpnewsAuthor = $('#mpnews-author'),
    $modalMpnews = $('#modal-mpnews'),
    $removeMpnews = $('#remove-mpnews'),
    $coverContainer = $('#cover-container'),

    // 短信
    smsMaxlength = $('#sms-maxlength').val(),
    $smsLength = $('#sms-length'),
    $contentSms = $('#content_sms'),
    $smsContent = $('#sms-content'),
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
// 初始化html5编辑器
initEditor();

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
$(document).on('change', '.file-upload', function () {
    if ($(this).val() !== '') { upload($(this)); }
});
// 初始化移除上传文件的事件
$(document).on('click', '.remove-file', function () {
    var $container = $messageContent.find('.tab-pane.active'),
        types = $(this).prev().attr('id').split('-'),
        type = types[types.length - 1];

    switch (type) {
        case 'mpnews':
            $container = $('#cover-container');
            break;
        case 'video':
            // btntxt = '上传视频';
            $container = $('#video-container');
            break;
        default:
            break;
    }
    $container.find('.upload-button').show();
    $container.find('.file-upload').val('');
    $container.find('.file-content').remove();
});

/** 图文 ------------------------------------------------------------------------------------------------------------- */
var mpnews = { articles: [] },  // 文章数组
    mpnewsCount = mpnews['articles'].length;    // 文章数量
// 添加图文
$addMpnews.on('click', function () {
    if (mpnewsCount === 8) {
        page.inform('消息中心', '最多添加8条图文', page.failure);
        return false;
    }
    $mpnewsId.val('');
    $mpnewsTitle.val('');
    $mpnewsContent.val('');
    $contentSourceUrl.val('');
    $mpnewsDigest.val('');
    $mpnewsAuthor.val('');
    $coverContainer.find('.file-upload').val('');
    $removeMpnews.hide();
    $coverContainer.find('.file-content').remove();
    $coverContainer.find('.upload-button').show();
    $modalMpnews.modal({ backdrop: true });
});
// 编辑图文
$(document).on('click', '.mpnews', function () {
    var ids = $(this).attr('id').split('-'),
        id = ids[ids.length - 1],
        news = mpnews['articles'][id];

    $mpnewsId.val(id);
    $mpnewsTitle.val(news['title']);
    $mpnewsContent.val(news['content']);
    $contentSourceUrl.val(news['content_source_url']);
    $mpnewsAuthor.val(news['author']);
    $mpnewsDigest.val(news['digest']);
    $coverContainer.find('.upload-button').hide();
    $coverContainer.find('.file-content').remove();
    $coverContainer.append(
        '<div class="file-content">' +
        '<label for="file-mpnews" style="margin-right: 10px;" class="custom-file-upload text-blue">' +
        '<i class="fa fa-pencil"> 更换</i>' +
        '</label>' +
        $('<input />', {'class': 'file-upload', id: 'file-mpnews', type: 'file', 'accept': 'image/*'}).prop('outerHTML') +
        '<a href="#" class="remove-file"><i class="fa fa-remove text-red"> 删除</i></a><br />' +
        $('<input />', {'class': 'media_id', type: 'hidden', value: news['thumb_media_id']}).prop('outerHTML') +
        $('<img' + ' />', {'src': news['image_url'], 'style': 'height: 200px;'}).prop('outerHTML') +
        '</div>'
    );
    $removeMpnews.show();
    $modalMpnews.modal({ backdrop: true });
});
// 保存图文
$formMpnews.parsley().on('form:validated', function () {
    if ($('.parsley-error').length === 0) {
        var imgAttrs = {},
            id = $mpnewsId.val(),
            title = $mpnewsTitle.val(),
            content = $mpnewsContent.val(),
            contentSourceUrl = $contentSourceUrl.val(),
            author = $mpnewsAuthor.val(),
            digest = $mpnewsDigest.val(),
            mediaId = $formMpnews.find('.media_id').val(),
            imageUrl = $coverContainer.find('img').attr('src'),
            article = {
                title: title,
                thumb_media_id: mediaId,
                author: author,
                content_source_url: contentSourceUrl,
                content: content,
                digest: digest,
                image_url: imageUrl
            };

        if (id === '') {
            // 新增图文
            mpnews['articles'].push(article);
            imgAttrs = {
                'class': 'mpnews',
                'src': imageUrl,
                'title': title,
                'id': 'mpnews-' + mpnewsCount
            };
            $contentMpnews.append($('<img' + ' />', imgAttrs).prop('outerHTML'));
            mpnewsCount += 1;
        } else {
            // 更新图文
            var $mpnews = $($contentMpnews.children('img')[id]);

            mpnews['articles'][id] = article;
            $mpnews.attr('src', imageUrl);
            $mpnews.attr('title', title);
        }
        $modalMpnews.modal('hide');
    }
    return false;
}).on('form:submit', function () {
    return false;
});
// 删除图文
$(document).on('click', '#remove-mpnews', function () {
    var id = $mpnewsId.val(), i = 0;

    // 从数组中移除图文
    mpnews['articles'].splice(id, 1);
    // 从图文列表中移除
    $('#mpnews-' + id).remove();
    // 重建图文索引
    $contentMpnews.find('img').each(function () {
        $(this).attr('id', '#mpnews-' + i);
        i++;
    });
    mpnewsCount--;
    page.inform('消息中心', '已将指定图文删除', page.success);
});

/** 短信 ------------------------------------------------------------------------------------------------------------- */
// 获取短信输入字符数
$smsLength.text('已输入0个字符， 还可输入' + smsMaxlength + '个字符');
$contentSms.attr('maxlength', smsMaxlength);
$smsContent.on('keyup', function () {
    currentLength = $(this).val().length;
    availableLength = smsMaxlength - currentLength;
    if (availableLength < 0) {
        var str = $smsContent.val();
        $smsContent.val(str.substring(0, smsMaxlength));
        return false;
    }
    $smsLength.text('已输入' + currentLength + '个字符， 还可输入' + availableLength + '个字符');
});

/** 发送消息 ---------------------------------------------------------------------------------------------------------- */
$send.on('click', function () {
    var appIds = $('#app_ids').val(),
        targetIds = $('#selected-node-ids').val(),
        types = $('#message-content').find('.tab-pane.active').attr('id').split('_'),
        type = types[types.length - 1],
        $container = $('#content_' + type),
        content = null,
        mediaId = '',
        formData = {
            _token: token,
            type: type,
            appIds: appIds,
            targetIds: targetIds,
            messageTypeId: $messageTypeId.val(),
        };

    switch (type) {
        case 'text':    // 文本
            content = { text: { content: $textContent.val() }};
            break;
        case 'image':   // 图片
            content = { image: { media_id: $container.find('.media_id').val() } };
            break;
        case 'audio':   // 语音
            content = { voice: { media_id: $container.find('.media_id').val() } };
            formData['type'] = 'voice';
            break;
        case 'video':   // 视频
            content = {
                video: {
                    media_id: $container.find('.media_id').val(),
                    title: $videoTitle.val(),
                    description: $videoDescription.val()
                }
            };
            break;
        case 'file':   // 文件
            content = { file: { media_id: $container.find('.media_id').val() } };
            break;
        case 'card':    // 卡片
            content = {
                textcard: {
                    title: $cardTitle.val(),
                    description: $cardDescription.val(),
                    url: $cardUrl.val(),
                    btntxt: $cardBtntxt.val()
                }
            };
            formData['type'] = 'textcard';
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
        data: $.extend(formData, content),
        success: function (result) {
            page.inform(result.title, result.message, page.success);
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
});

/** Helper functions ------------------------------------------------------------------------------------------------ */
// 上传文件 (图片、语音、视频、文件、封面图）
function upload($file) {
    var file = $file[0].files[0],
        types = $file.attr('id').split('-'),
        type = types[types.length - 1],
        names = file.name.split('.'),
        imgAttrs = {},
        ext = names[names.length - 1].toUpperCase();

    if ($.inArray(ext, ['JPG', 'PNG', 'AMR', 'MP4']) === -1) {
        return warning('不支持这种文件格式');
    }
    page.inform('消息中心', '文件上传中...', page.info);
    $('.overlay').show();
    var data = new FormData();
    data.append('file', file);
    data.append('_token', token);
    data.append('type', type === 'mpnews' ? 'image' : type);
    $.ajax({
        type: 'POST',
        url: page.siteRoot() + "messages/index",
        data: data,
        contentType: false,
        processData: false,
        success: function (result) {
            $('.overlay').hide();
            page.inform(result.title, result.message, page.success);
            var html =
                '<label for="file-' + type + '" style="margin-right: 10px;" class="custom-file-upload text-blue">' +
                '<i class="fa fa-pencil"> 更换</i>' +
                '</label>' +
                $('<input />', {'class': 'file-upload', id: 'file-' + type, type: 'file', 'accept': type + '/*'}).prop('outerHTML') +
                '<a href="#" class="remove-file"><i class="fa fa-remove text-red"> 删除</i></a><br />' +
                $('<input />', {'class': 'media_id', type: 'hidden', value: result.data.media_id}).prop('outerHTML') +
                $('<input />', {'class': 'media-id', type: 'hidden', value: result.data.id}).prop('outerHTML'),
                $container = $messageContent.find('.tab-pane.active');
            switch (type) {
                case 'image':
                    imgAttrs = {
                        'src': '../../' + result.data.path,
                        'style': 'height: 200px;',
                        'title': '文件名：' + result.data.filename
                    };
                    html += $('<img' + ' />', imgAttrs).prop('outerHTML');
                    break;
                case 'voice':
                    html += $('<i>', {'class': 'fa fa-file-sound-o'}).prop('outerHTML') +
                        $('<span>', {id: 'voice'}).prop('innerHTML', result.data.filename).prop('outerHTML');
                    break;
                case 'video':
                    html += '<video width="400" controls><source src="../../' + result.data.path + '" type="video/mp4"></video>';
                    $container = $('#video-container');
                    break;
                case 'file':
                    html += $('<i>', {'class': 'fa fa-file'}).prop('outerHTML') +
                        $('<span>', {id: 'file'}).prop('innerHTML', result.data.filename).prop('outerHTML');
                    break;
                case 'mpnews':
                    imgAttrs = {
                        'src': '../../' + result.data.path,
                        'style': 'height: 200px;',
                        'title': '文件名：' + result.data.filename
                    };
                    html += $('<img' + ' />', imgAttrs).prop('outerHTML');
                    $container = $('#cover-container');
                    break;
                default:
                    return false;
            }
            $container.find('.upload-button').hide();
            $container.find('.file-content').remove();
            $container.append(
                $('<div>', {'class': 'file-content'}).prop('innerHTML', html).prop('outerHTML')
            );
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
}

function warning(message) {
    page.inform('上传文件', message, page.failure);
    return false;
}
function initEditor() {
    page.loadCss(plugins.htmleditor.css);
    $.getMultiScripts([plugins.handlebar.js]).done(function () {
        $.getMultiScripts([plugins.htmleditor.all]).done(function () {
            $.getMultiScripts([plugins.htmleditor.js]).done(function () {
                $.getMultiScripts([plugins.htmleditor.locale]).done(function () {
                    var options = {
                        toolbar: {
                            html: true,
                            size: 'xs',
                            font_styles: true
                        },
                        locale: 'zh-CN'
                    };
                    $('#card-description').wysihtml5(options);
                    $('#mpnews-content').wysihtml5(options);
                });
            });
        });
    });
}