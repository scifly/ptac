//# sourceURL=index.js
var $targetIds = $('#selected-node-ids'),
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
    $preview = $('#preview');

// 初始化select2控件
page.initSelect2([{
    option: {
        templateResult: page.formatStateImg,
        templateSelection: page.formatStateImg
    },
    id: 'app_ids'
}]);
$.getMultiScripts([plugins.select2.js]).done(function () {
    $.getMultiScripts([plugins.select2.jscn]).done(function () {
        $messageTypeId.select2();
    });
});
// 初始化"已发送"datatable
var options = [
    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6]}
];
$('.box-tools').hide();
page.initDatatable('messages', options);
$('a[href="#tab02"]').on('click', function () {
    $('#data-table').dataTable().fnDestroy();
    page.initDatatable('messages', options);
});
$('.action-type').on('click', function () {
    if ($(this).find('a').attr('href') === '#tab02') {
        $('.box-tools').slideDown();
    } else {
        $('.box-tools').slideUp();
    }
});
$(document).on('click', '.fa-edit', function() {
    var paths = $(this).parents().eq(0).attr('id').split('_'),
        id = paths[1];
    $('a[href="#tab02"]').parent().removeClass('active');
    $('#tab02').removeClass('active');
    $('a[href="#tab01"]').parent().addClass('active');
    $('#tab01').addClass('active');
    $('.box-tools').hide();
    $('.overlay').show();
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: page.siteRoot() + 'messages/edit/' + id,
        success: function (result) {
            var $msgTypeId = $('#message_type_id'), $tabTitle;

            console.log(result);
            $msgTypeId.val(result['messageTypeId']).trigger('change');
            $('#checked-nodes').html(result['targets']);
            $('#selected-node-ids').val(result['selectedTargetIds'].join(','));

            $messageContent.find('.tab-pane').hide();
            $('#message-format li').removeClass('active');
            $('#message-format a').removeClass('text-blue');
            $tabTitle = $('a[href="#content_' + result['message']['msgtype'] + '"]');
            $tabTitle.parent().addClass('active');
            $tabTitle.addClass('text-blue');
            $('#content_' + result['message']['msgtype']).show();
            switch (result['message']['msgtype']) {
                case 'text':
                    $textContent.val(result['message']['text']['content']);
                    break;
                case 'image':
                    break;
                default:
                    break;
            }
            $('.overlay').hide();
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
});

// 初始化消息类型卡片悬停特效、input parsley验证规则
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
    initUpload();
    $messageContent.find(':input').removeAttr(
        'required data-parsley-length maxlength'
    );
    $('#message-content .tab-pane').hide();
    switch ($(this).attr('href')) {
        case '#content_text':
            $textContent.attr('required', 'true');
            break;
        case '#content_image':
            $fileImage.attr('required', 'true');
            break;
        case '#content_audio':
            $fileAudio.attr('required', 'true');
            break;
        case '#content_video':
            $videoTitle.attr('maxlength', 128);
            $videoDescription.attr('maxlength', 512);
            $fileVideo.attr('required', 'true');
            break;
        case '#content_file':
            $fileFile.attr('required', 'true');
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
    $($(this).attr('href')).show();
});
page.refreshTabs();
// 加载消息中心css
page.loadCss('css/message/message.css');
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


/** 发送对象 ---------------------------------------------------------------------------------------------------------- */
// 选择发送对象
$choose.on('click', function () {
    $message.hide();
    $targets.show();
});
// 部门及联系人树加载
$.getMultiScripts(['js/tree.js']).done(
    function () { $.tree().list('messages/index', 'contact'); }
);
// 关闭发送对象选择窗口
$(document).on('click', '#cancel .close-targets', function () {
    $message.show();
    $targets.hide();
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
    $targetIds.attr('required', 'true');
    return send(false);
});

$preview.on('click', function () {
    $targetIds.removeAttr('required');
    return send(true);
});

/** Helper functions ------------------------------------------------------------------------------------------------ */
function send(preview) {
    var appIds = [$('#app_ids').val()],
        targetIds = $('#selected-node-ids').val(),
        types = $('#message-content').find('.tab-pane.active').attr('id').split('_'),
        type = types[types.length - 1],
        $container = $('#content_' + type),
        content = null, formData;

    if (preview) {
        targetIds = 'user-0-' + $('#userId').val();
    }
    formData = {
        _token: page.token(),
        type: type,
        app_ids: appIds,
        targetIds: targetIds,
        message_type_id: $messageTypeId.val(),
    };
    if (!$('#formMessage').parsley().validate()) { return false; }
    switch (type) {
        case 'text':    // 文本
            $textContent.attr('required', 'true');
            content = { text: { content: $textContent.val() }};
            break;
        case 'image':   // 图片
            content = {
                image: {
                    media_id: $container.find('.media_id').val(),
                    path: $container.find('.media_id').attr('data-path')
                }
            };
            break;
        case 'audio':   // 语音
            content = {
                voice: {
                    media_id: $container.find('.media_id').val(),
                    path: $container.find('.media_id').attr('data-path')
                }
            };
            formData['type'] = 'voice';
            break;
        case 'video':   // 视频
            content = {
                video: {
                    media_id: $container.find('.media_id').val(),
                    title: $videoTitle.val(),
                    description: $videoDescription.val(),
                    path: $container.find('.media_id').attr('data-path')
                }
            };
            break;
        case 'file':   // 文件
            content = {
                file: {
                    media_id: $container.find('.media_id').val(),
                    path: $container.find('.media_id').attr('data-path')
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
            content = {sms: $('#contentSms').val()};
            break;
        default:
            break;
    }

    $.ajax({
        url: page.siteRoot() + "messages/store",
        type: 'POST',
        dataType: 'json',
        data: $.extend(formData, content),
        success: function (result) {
            page.inform(result.title, !preview ? result.message : '预览消息已发送至您的手机微信，请打开相关应用查看', page.info);
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });

    return false;
}
// 上传文件 (图片、语音、视频、文件、封面图）
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
            var html = '', $container = $messageContent.find('.tab-pane.active');

            switch (type) {
                case 'image':
                    imgAttrs = {
                        'src': '../../' + result.data.path,
                        'style': 'height: 200px;',
                        'title': '文件名：' + result.data.filename
                    };
                    html += $('<img' + ' />', imgAttrs).prop('outerHTML');
                    break;
                case 'audio':
                    html += $('<i>', {'class': 'fa fa-file-sound-o'}).prop('outerHTML') + ' ' +
                        $('<span>').prop('innerHTML', result.data.filename).prop('outerHTML');
                    break;
                case 'video':
                    html += '<video width="400" controls><source src="../../' + result.data.path + '" type="video/mp4"></video>';
                    $container = $('#video-container');
                    break;
                case 'file':
                    html += $('<i>', {'class': 'fa fa-file-o'}).prop('outerHTML') + ' ' +
                        $('<span>').prop('innerHTML', result.data.filename).prop('outerHTML');
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
            $container.find('.media_id').val(result.data.media_id).attr('data-path', result.data.path);

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
        },
        error: function (e) {
            page.errorHandler(e);
            $('.file-upload').val('');
        }
    });
    return false;
}
function initUpload() {
    $(document).off('change', '.file-upload').on('change', '.file-upload', function () {
        if ($(this).val() !== '') { upload($(this)); }
    });
}
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