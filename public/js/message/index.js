//# sourceURL=index.js
// noinspection JSUnusedGlobalSymbols
var $batchBtns = $('.box-tools'),
    $tabSend = $('a[href="#tab01"]'),
    $tabSent = $('a[href="#tab02"]'),
    $targetIds = $('#selected-node-ids'),
    $message = $('#message'),
    $messageTypeId = $('#message_type_id'),
    $messageContent = $('#message-content'),

    // 发送对象
    $targets = $('#targets'),
    $choose = $('#choose'),

    // 文本
    $textContent = $('#text-content'),

    // 图片
    $fileImage = $('#file-image'),

    // 语音
    $fileAudio = $('#file-audio'),

    // 视频
    $videoTitle = $('#video-title'),
    $videoDescription = $('#video-description'),
    $fileVideo = $('#file-video'),

    // 文件
    $fileFile = $('#file-file'),

    // 卡片
    $cardTitle = $('#card-title'),
    $cardDescription = $('#card-description'),
    $cardUrl = $('#card-url'),
    $cardBtntxt = $('#card-btntxt'),

    // 图文
    $contentMpnews = $('#content_mpnews'),
    $formMpnews = $('#formMpnews'),
    $fileMpnews = $('#file-mpnews'),
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
    $send = $('#send'),
    $preview = $('#preview'),
    $schedule = $('#schedule'),
    $draft = $('#draft');

// 初始化
init();

/** 发消息 ----------------------------------------------------------------------------------------------------------- */
/** 发送对象 */
// 选择发送对象
$choose.on('click', function () {
    $message.hide();
    $targets.show();
});
// 部门及联系人树加载
$.getMultiScripts(['js/tree.js']).done(
    function () {
        $.tree().list('messages/index', 'contact');
    }
);
// 关闭发送对象选择窗口
$(document).on('click', '#cancel .close-targets',
    function () {
        $message.show();
        $targets.hide();
    }
);
/** 图文 */
var mpnews = { articles: [] },  // 文章数组
    mpnewsCount = mpnews['articles'].length;    // 文章数量
// 添加图文
$addMpnews.on('click', function () {
    if (mpnewsCount === 8) {
        page.inform('消息中心', '最多添加8条图文', page.failure);
        return false;
    }
    var $uploadBtn = $coverContainer.find('.upload-button'),
        $label = $uploadBtn.find('label'),
        $removeFile = $uploadBtn.find('.remove-file'),
        $mediaId = $uploadBtn.find('.media_id'),
        $file = $mediaId.next();

    $mpnewsId.val('');
    $mpnewsTitle.val('');
    $mpnewsContent.val('');
    $contentSourceUrl.val('');
    $mpnewsDigest.val('');
    $mpnewsAuthor.val('');
    $removeMpnews.hide();
    $removeFile.hide();
    $mediaId.val('');
    $label.html('<i class="fa fa-cloud-upload"></i> 上传封面图');
    if ($file.attr('class') !== 'help-block') {
        $file.remove();
    }
    $fileMpnews.val('');
    $modalMpnews.modal({ backdrop: true });
});
// 编辑图文
$(document).on('click', '.mpnews', function () {
    var ids = $(this).attr('id').split('-'),
        id = ids[ids.length - 1],
        news = mpnews['articles'][id],
        $uploadBtn = $coverContainer.find('.upload-button'),
        $label = $uploadBtn.find('label'),
        $removeFile = $uploadBtn.find('.remove-file'),
        $mediaId = $uploadBtn.find('.media_id'),
        $file = $mediaId.next();

    $mpnewsId.val(id);
    $mpnewsTitle.val(news['title']);
    $mpnewsContent.val(news['content']);
    $contentSourceUrl.val(news['content_source_url']);
    $mpnewsAuthor.val(news['author']);
    $mpnewsDigest.val(news['digest']);
    $label.html('<i class="fa fa-pencil"></i> 更换');
    $removeFile.show();
    $mediaId.val(news['thumb_media_id']);
    if ($file.attr('class') !== 'help-block') { $file.remove(); }
    $mediaId.after($('<img' + ' />', {'src': news['image_url'], 'style': 'height: 200px;'}).prop('outerHTML'));
    $removeMpnews.show();
    $modalMpnews.modal({ backdrop: true });
});
// 保存/更新图文
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
/** 短信 */
// 获取短信输入字符数
$smsLength.text('已输入0个字符， 还可输入' + smsMaxlength + '个字符');
$contentSms.attr('maxlength', smsMaxlength);
$smsContent.on('keyup change', function () {
    currentLength = $(this).val().length;
    availableLength = smsMaxlength - currentLength;
    if (availableLength < 0) {
        var str = $smsContent.val();
        $smsContent.val(str.substring(0, smsMaxlength));
        return false;
    }
    $smsLength.text('已输入' + currentLength + '个字符， 还可输入' + availableLength + '个字符');
});
/** 发送/预览/定时发送/存为草稿 */
$send.on('click', function () {
    $targetIds.attr('required', 'true');
    return message('send');
});
$preview.on('click', function () {
    $targetIds.removeAttr('required');
    return message('preview');
});
$draft.on('click', function () {
    return message('draft');
});

/** 已发送 ----------------------------------------------------------------------------------------------------------- */
// 初始化"已发送"datatable
var options = [{
    className: 'text-center', targets: [2, 3, 4, 5, 6]
}];
// 消息列表
page.initDatatable('messages', options);
// 重新加载datatable
$tabSent.on('click', function () { reloadDatatable(options); });
// 显示/隐藏批处理按钮组
$('.action-type').on('click', function () {
    if ($(this).find('a').attr('href') === '#tab02') {
        $batchBtns.slideDown();
    } else {
        $batchBtns.slideUp();
    }
});
// 编辑草稿
$(document).on('click', '.fa-edit', function () {
    var id = getMessageId($(this));

    $tabSent.parent().removeClass('active');
    $tabSent.removeClass('active');
    $tabSend.parent().addClass('active');
    $('#tab01').addClass('active');
    $batchBtns.hide();
    $('#id').val(id);
    $('.overlay').show();
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: page.siteRoot() + 'messages/edit/' + id,
        success: function (result) {
            var $msgTypeId = $('#message_type_id'), $tabTitle,
                html = '', type = result['message']['msgtype'],
                $container, mediaId, src,
                uploadTypes = ['image', 'audio', 'video', 'file'];

            if (type === 'textcard') { type = 'card'; }
            if (type === 'voice') { type = 'audio'; }

            $container = $('#content_' + type);
            // 设置消息类型
            $msgTypeId.val(result['messageTypeId']).trigger('change');
            // 显示发送对象列表
            $('#checked-nodes').html(result['targets']);
            // 设置发送对象id
            $('#selected-node-ids').val(result['selectedTargetIds'].join(','));
            // 隐藏所有类型消息内容
            $messageContent.find('.tab-pane').removeClass('active').hide();
            $container.addClass('active');
            $('#message-format li').removeClass('active');
            $('#message-format a').removeClass('text-blue').addClass('text-gray');
            $tabTitle = $('a[href="#content_' + type + '"]');
            $tabTitle.parent().addClass('active');
            $tabTitle.removeClass('text-gray').addClass('text-blue');

            $container.show();
            if ($.inArray(type, uploadTypes) > -1) {
                mediaId = result['message'][type === 'audio' ? 'voice' : type]['media_id'];
                src = result['message'][type === 'audio' ? 'voice' : type]['path'];
            }
            removeValidation();
            refreshValidation('#content_' + type);
            switch (type) {
                case 'text':
                    $textContent.val(result['message'][type]['content']);
                    break;
                case 'image':
                    var imgAttrs = {
                        'src':  src,
                        'style': 'height: 200px;',
                        'title': '文件名：' + filename(src)
                    };
                    html += $('<img' + ' />', imgAttrs).prop('outerHTML');
                    break;
                case 'audio':
                    html += '<i class="fa fa-file-sound-o"> ' + filename(src) + '</i>';
                    break;
                case 'video':
                    var video = result['message']['video'];
                    $videoTitle.val(video['title']);
                    $videoDescription.val(video['description']);
                    html += '<video height="200" controls><source src="' + src + '" type="video/mp4"></video>';
                    $container = $('#video-container');
                    break;
                case 'file':
                    html += '<i class="fa fa-file-o"> ' + filename(src) + '</i>';
                    break;
                case 'card':
                    var card = result['message']['textcard'];
                    $cardTitle.val(card['title']);
                    $cardDescription.val(card['description']);
                    $cardUrl.val(card['url']);
                    $cardBtntxt.val(card['btntxt']);
                    break;
                case 'mpnews':
                    var mpnewsList = '';
                    mpnews = result['message'][type];
                    mpnewsCount = mpnews['articles'].length;
                    $addMpnews.siblings().remove();
                    for (var i = 0; i < mpnewsCount; i++) {
                        imgAttrs = {
                            'class': 'mpnews',
                            'src': mpnews['articles'][i]['image_url'],
                            'title': mpnews['articles'][i]['title'],
                            'id': 'mpnews-' + i
                        };
                        mpnewsList += $('<img' + ' />', imgAttrs).prop('outerHTML');
                    }
                    $addMpnews.after(mpnewsList);
                    break;
                case 'sms':
                    $smsContent.val(result['message'][type]).trigger('change');
                    break;
                default:
                    break;
            }
            if ($.inArray(type, uploadTypes) > -1) {
                displayFile($container, mediaId, src, html);
            }
            $('.overlay').hide();
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
});
// 查看已发送消息详情
$(document).on('click', '.fa-laptop', function () {
    $.ajax({
        type: 'GET',
        dataType: 'html',
        url: page.siteRoot() + 'messages/show/' + getMessageId($(this)),
        success: function (result) {
            var $show = $('#modal-show');
            $show.find('.modal-body').html(result);
            $show.modal({backdrop: true});
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
});
// 删除消息
page.remove('messages', options);

/** Helper functions ------------------------------------------------------------------------------------------------ */
function init() {
    // 加载消息中心css
    page.loadCss('css/message/message.css');
    // 初始化应用select2控件
    page.initSelect2([{
        option: {
            templateResult: page.formatStateImg,
            templateSelection: page.formatStateImg
        },
        id: 'app_ids'
    }]);
    // 初始化消息类型select2控件
    $.getMultiScripts([plugins.select2.js]).done(function () {
        $.getMultiScripts([plugins.select2.jscn]).done(function () {
            $messageTypeId.select2();
        });
    });
    // 隐藏'已发送'消息列表对应的批处理按钮组
    $batchBtns.hide();
    // 初始化消息类型卡片悬停特效、input parsley验证规则
    initTabs();
    // 初始化上传文件的事件
    initUpload();
    // 初始化移除上传文件的事件
    $(document).on('click', '.remove-file', function () {
        var $container = $messageContent.find('.tab-pane.active'),
            types = $(this).prev().attr('id').split('-'),
            type = types[types.length - 1],
            label = '',
            $uploadBtn = $container.find('.upload-button'),
            $label = $uploadBtn.find('label'),
            $removeFile = $uploadBtn.find('.remove-file'),
            $mediaId = $uploadBtn.find('.media_id'),
            $file = $mediaId.next();

        switch (type) {
            case 'image':
                label = '上传图片';
                break;
            case 'audio':
                label = '上传语音';
                break;
            case 'video':
                label = '上传视频';
                $container = $('#video-container');
                break;
            case 'file':
                label = '上传文件';
                break;
            case 'mpnews':
                label = '上传封面图';
                $container = $('#cover-container');
                break;
            default:
                break;
        }
        $label.html('<i class="fa fa-cloud-upload"></i> ' + label);
        $removeFile.hide();
        $mediaId.val('');
        $file.remove();
        $('#file-' + type).val('');
    });
    // 初始化html5编辑器
    // initEditor();
}
function message(action) {
    var icon = page.info,
        uri = 'send',
        requestType = 'POST',
        formData = data();

    switch (action) {
        case 'send':
            break;
        case 'preview':
            formData = data(true);
            break;
        case 'draft':
            var $id = $('#id');
            uri = $id.val() !== '' ? 'update/' + $id.val() : 'store';
            requestType = $id.val() !== '' ? 'PUT' : 'POST';
            icon = page.success;
            break;
        case 'schedule':
            break;
        default:
            break;
    }
    if (!$('#formMessage').parsley().validate()) { return false; }
    if ($targetIds.val() === '') {
        page.inform('消息中心', '请选择发送对象', page.failure);
        return false;
    }

    $.ajax({
        url: page.siteRoot() + 'messages/' + uri,
        type: requestType,
        dataType: 'json',
        data: formData,
        success: function (result) {
            page.inform(result.title, result.message, icon);
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });

    return false;
}
// 获取消息表单数据
function data(preview = false) {
    var appIds = [$('#app_ids').val()],
        targetIds = preview ? 'user-0-' + $('#userId').val() : $('#selected-node-ids').val(),
        types = $('#message-content').find('.tab-pane.active').attr('id').split('_'),
        type = types[types.length - 1],
        $container = $('#content_' + type),
        messageId = $('#id').val(),
        content = null, formData,
        mediaId, path, uploadTypes = ['image', 'audio', 'video', 'file'];

    formData = {
        _token: page.token(),
        type: type,
        app_ids: appIds,
        targetIds: targetIds,
        message_type_id: $messageTypeId.val(),
    };
    if (preview) { $.extend(formData, { preview: 1 }); }
    if (messageId !== '') { $.extend(formData, { id: messageId }); }

    if ($.inArray(type, uploadTypes) > -1) {
        mediaId = $container.find('.media_id').val();
        path = $container.find('.media_id').attr('data-path');
    }
    switch (type) {
        case 'text':    // 文本
            $textContent.attr('required', 'true');
            content = { text: { content: $textContent.val() }};
            break;
        case 'image':   // 图片
            content = {
                image: {
                    media_id: mediaId,
                    path: path
                }
            };
            break;
        case 'audio':   // 语音
            content = {
                voice: {
                    media_id: mediaId,
                    path: path
                }
            };
            formData['type'] = 'voice';
            break;
        case 'video':   // 视频
            content = {
                video: {
                    media_id: mediaId,
                    title: $videoTitle.val(),
                    description: $videoDescription.val(),
                    path: path
                }
            };
            break;
        case 'file':   // 文件
            content = {
                file: {
                    media_id: mediaId,
                    path: path
                }
            };
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
            content = {
                mpnews: {
                    articles: mpnews['articles']
                }
            };
            break;
        case 'sms': // 短信
            appIds = [0];
            content = {sms: $smsContent.val()};
            break;
        default:
            break;
    }

    return $.extend(formData, content);
}
// 初始化上传文件 (图片、语音、视频、文件、封面图）
function initUpload() {
    $(document).off('change', '.file-upload').on('change', '.file-upload', function () {
        if ($(this).val() !== '') { upload($(this)); }
    });
}
function upload($file) {
    var file = $file[0].files[0],
        types = $file.attr('id').split('-'),
        type = types[types.length - 1],
        names = file.name.split('.'),
        imgAttrs = {},
        ext = names[names.length - 1].toUpperCase();

    if ($.inArray(ext, ['JPG', 'PNG', 'AMR', 'MP4']) === -1 && type !== 'file') {
        page.inform('消息中心', '不支持这种文件格式', page.failure);
        return false;
    }
    page.inform('消息中心', '文件上传中...', page.info);
    $('.overlay').show();
    var data = new FormData();
    data.append('file', file);
    data.append('_token', page.token());
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
            var html = '',
                $container = $('#content_' + type),
                filename = result['data']['filename'],
                src = '../../' + result['data']['path'];

            switch (type) {
                case 'image':
                    imgAttrs = {
                        'src': src,
                        'style': 'height: 200px;',
                        'title': '文件名：' + filename
                    };
                    html += $('<img' + ' />', imgAttrs).prop('outerHTML');
                    break;
                case 'audio':
                    html += '<i class="fa fa-file-sound-o"> ' + filename + '</i>';
                    break;
                case 'video':
                    html += '<video height="200" controls><source src="' + src + '" type="video/mp4"></video>';
                    $container = $('#video-container');
                    break;
                case 'file':
                    html += '<i class="fa fa-file-o"> ' + filename + '</i>';
                    break;
                case 'mpnews':
                    imgAttrs = {
                        'src': src,
                        'style': 'height: 200px;',
                        'title': '文件名：' + filename
                    };
                    html += $('<img' + ' />', imgAttrs).prop('outerHTML');
                    $container = $('#cover-container');
                    break;
                default:
                    return false;
            }
            displayFile($container, result['data']['media_id'], src, html);
        },
        error: function (e) {
            page.errorHandler(e);
            $('.file-upload').val('');
        }
    });
    return false;
}
function initTabs() {
    $('.tab').hover(
        function () {
            $(this).removeClass('text-gray').addClass('text-blue');
        },
        function () {
            if (!($(this).parent().hasClass('active'))) {
                $(this).removeClass('text-blue').addClass('text-gray');
            }
        }
    ).click(function () {
        var anchor = $(this).attr('href');

        initUpload();
        removeValidation();
        $messageContent.find('.tab-pane').hide();
        refreshValidation(anchor);
        $(anchor).show();
    });
    page.refreshTabs();
}
function displayFile($container, mediaId, src, html) {
    $container.find('.media_id').val(mediaId).attr('data-path', src);

    var $uploadBtn = $container.find('.upload-button'),
        $label = $uploadBtn.find('label'),
        $mediaId = $uploadBtn.find('.media_id'),
        $removeFile = $uploadBtn.find('.remove-file'),
        $file = $mediaId.next();

    $label.html('<i class="fa fa-pencil"> 更换</i>');
    $removeFile.show();
    if ($file.attr('class') !== 'help-block') {
        $file.remove();
    }
    $mediaId.after(html);
}
function filename(uri) {
    var paths = uri.split('/');
    return paths[paths.length - 1];
}
function removeValidation() {
    $messageContent.find(':input').removeAttr(
        'required data-parsley-length maxlength'
    );
}
function refreshValidation(anchor) {
    // $targetIds.attr('required', 'true');
    switch (anchor) {
        case '#content_text':
            $textContent.attr('required', 'true');
            break;
        case '#content_image':
        case '#content_audio':
        case '#content_file':
            $(anchor).find('.media_id').attr('required', 'true');
            break;
        case '#content_video':
            $videoTitle.attr('maxlength', 128);
            $videoDescription.attr('maxlength', 512);
            $(anchor).find('.media_id').attr('required', 'true');
            break;
        case '#content_card':
            $cardTitle.attr({
                'required': 'true',
                'data-parsley-length': '[2,128]',
            });
            $cardDescription.attr({
                'required': 'true',
                'data-parsley-length': '[2,512]'
            });
            $cardUrl.attr({
                'required': 'true',
                'type': 'url'
            });
            break;
        case '#content_mpnews':
            break;
        case '#content_sms':
            $smsContent.attr({
                'required': 'true',
                'data-parsley-length': '[2,300]'
            });
            break;
        default:
            break;
    }
}
function reloadDatatable(options) {
    $('#data-table').dataTable().fnDestroy();
    page.initDatatable('messages', options);
}
function getMessageId($button) {
    var paths = $button.parents().eq(0).attr('id').split('_');
    return paths[1];
}
// noinspection JSUnusedGlobalSymbols
function initEditor() {
    page.loadCss(plugins.htmleditor.css);
    $.getMultiScripts([plugins.handlebar.js]).done(function () {
        $.getMultiScripts([plugins.htmleditor.all]).done(function () {
            $.getMultiScripts([plugins.htmleditor.js]).done(function () {
                $.getMultiScripts([plugins.htmleditor.locale]).done(function () {
                    var options = {
                        toolbar: {
                            html: false,
                            size: 'xs',
                            fa: true,
                            color: true,
                            font_styles: false
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