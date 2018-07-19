var message = {
        text: { content: '' },
        image: { media_id: '', media_name: '', type: 'image/*' },
        voice: { media_id: '', media_name: '', type: 'audio/*' },
        video: {
            media_id: '', media_name: '', type: 'video/*',
            title: '', description: '',
        },
        file: { media_id: '', media_name: '', type: '' },
        textcard: {
            title: '',
            description: '',
        },
        sms: ''
    },
    $search = $('#search'),
    $send = $('#send'),
    $notification = $('#notification'),

    // 发送对象
    $targetsContainer = $('#targets-container'),
    $targetCheck = $('.target-check'),
    $checkAll = $('#check-all'),
    $confirm = $('#confirm'),
    $chosenResults = $('#chosen-results'),

    // 信息类型、消息类型
    $msgType = $('#msg-type'),
    $messageTypeId = $('#message_type_id'),

    // 消息 - 文本、图片、语音、视频、文件、卡片、短信
    $extra = $('.extra'),   // 消息的附加属性(文件、url、图片、语音等)
    $titleContainer = $('#title-container'),
    $title = $('#title'),
    $contentContainer = $('#content-container'),
    $content = $('#content'),
    $cardUrlContainer = $('#card-url-container'),
    $cardUrl = $('#card-url'),
    $btnTxtContainer = $('#btn-txt-container'),
    $btnTxt = $('#btn-txt'),
    $mediaId = $('#media_id'),
    $uploadContainer = $('#upload-container'),
    $upload = $('#upload'),
    $uploadTitle = $('#upload-title'),

    // 消息 - 图文
    $mpnews = $('#mpnews'),
    mpnews = { articles: [] },  // 文章数组
    mpnewsCount = mpnews['articles'].length,    // 文章数量
    $mpnewsList = $('#mpnews-list'),
    $mpContainer = $('#mpnews-container'),
    $addMpnews = $('#add-mpnews'),
    $mpnewsId = $('#mpnews-id'),
    $mpTitle = $('#mpnews-title'),
    $mpContent = $('#mpnews-content'),
    $mpUrl = $('#content-source-url'),
    $mpAuthor = $('#author'),
    $mpDigest = $('#digest'),
    $mpUploadTitle = $('#mp-upload-title'),
    $mpUpload = $('#mpnews-upload'),
    $mpFilePath = $('#mp-file-path'),
    $mpMediaId = $('#thumb_media_id'),
    $add = $('#add'),
    $delete = $('#delete'),

    maxSize = 1024 * 1024,  // 1024KB，也就是 1MB
    maxWidth = 300, // 图片最大宽度
    tmp = 1,
    title = $title.val(),
    content = '';

/** 发送对象 */
// 初始化确认选定发送对象的事件
$confirm.on('click', function () {
    var html = $chosenResults.html();
    $chosenResults.html(html);
    $.closePopup();
});
// 选择所有发送对象
$checkAll.on('change', function () {
    if ($(this).is(':checked')) {
        var html = '';

        $targetCheck.prop('checked', true);
        $('.js-chosen-items .weui-check__label').each(function (i, target) {
            var $target = $(target),
                type = $target.attr('data-type'),
                id = $target.attr('data-item'),
                imgSrc = $target.find('img').attr('src');

            html += chosenHtml(id, type, imgSrc);
        });
        $chosenResults.html(html);
        removeTarget();
        $targetsContainer.addClass('air-checkall');
        countTargets();
    } else {
        $targetCheck.prop('checked', false);
        $chosenResults.html('');
        $targetsContainer.removeClass('air-checkall');
        countTargets();
    }
});
// 选择单个发送对象
$(document).on('change', '.target-check', function () {
    var $this = $(this).parents('.weui-check__label'),
        id = $this.attr('data-item'),
        type = $this.attr('data-type'),
        html = '';

    if ($(this).is(':checked')) {
        var imgSrc = $this.find('img').attr('src');
        html += chosenHtml(id, type, imgSrc);
        $chosenResults.prepend(html);
    } else {
        $chosenResults.find('#' + type + '-' + id).remove();
        $targetsContainer.removeClass('air-checkall');
        $checkAll.prop('checked', false);
        removeTarget();
    }
    countTargets();
});
// 移除发送对象
$(document).on('click', '.js-chosen-results-item', function () {
    removeTarget();
});
// 搜索发送对象
$search.on("input propertychange change", function () {
    var $targets = $('.targets'),
        type = $targets.length !== 0 ? 'department' : 'user',
        keyword = $(this).val(), i,
        data = {
            keyword: keyword,
            target: type,
            _token: wap.token()
        };

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'create',
        data: type === 'user'
            ? $.extend(data, {deptId: $('#deptId').val()})
            : data,
        success: function (result) {
            var html = result['targets'].length === 0 ? '暂无' : '';
            for (i = 0; i < result['targets'].length; i++) {
                html += targetHtml(result['targets'][i], type);
            }
            $targetsContainer.html(html);
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
});
// 返回部门列表
$('#back').on('click', function () {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'create',
        data: {
            target: 'list',
            _token: wap.token()
        },
        success: function (result) {
            var html = '';
            $('#back').hide();
            for (var i = 0; i < result['targets'].length; i++) {
                html += targetHtml(result['targets'][i], 'department');
            }
            $targetsContainer.html(html);
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
});
// 显示指定部门的用户(监护人)列表
$(document).on('click', '.targets', function () {
    var ids = $(this).prev().attr('id').split('-'),
        id = ids[ids.length - 1];
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'create',
        data: {
            target: 'user',
            departmentId: id,
            _token: wap.token()
        },
        success: function (result) {
            var html = result['targets'].length === 0 ? '暂无' : '';
            $('#back').show();
            $('#deptId').val(id);
            for (var i = 0; i < result['targets'].length; i++) {
                html += targetHtml(result['targets'][i], 'user');
            }
            $targetsContainer.html(html);
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
});

/** 信息类型 */
// 初始化选择信息类型的事件
$msgType.on('change', function () {
    var type = $(this).val();
    $('.js-content-item input').val('');
    $content.html('');
    switch (type) {
        case 'text':
            $extra.hide();
            $titleContainer.hide();
            $mpContainer.hide();
            $contentContainer.show();
            $content.html(message[type]['content']);
            break;
        case 'image':
            $extra.hide();
            $titleContainer.hide();
            $contentContainer.hide();
            $mpContainer.hide();
            $uploadContainer.show();
            $mediaId.val(message[type]['media_id']);
            $upload.attr('accept', 'image/*');
            $uploadTitle.text('上传图片');
            break;
        case 'voice':
            $extra.hide();
            $titleContainer.hide();
            $contentContainer.hide();
            $mpContainer.hide();
            $uploadContainer.show();
            $mediaId.val(message[type]['media_id']);
            $upload.attr('accept', 'audio/*');
            $uploadTitle.text('上传语音');
            break;
        case 'video':
            $extra.hide();
            $mpContainer.hide();
            $uploadTitle.text('上传视频');
            $title.attr('placeholder', '视频标题').val(message[type]['title']);
            $content.attr('placeholder', '视频描述').html(message[type]['description']);
            $upload.attr('accept', message[type]['type']);
            $mediaId.val(message[type]['media_id']);
            $titleContainer.show();     // title
            $contentContainer.show();   // description
            $uploadContainer.show();    // media_id
            break;
        case 'file':
            $extra.hide();
            $titleContainer.hide();
            $contentContainer.hide();
            $mpContainer.hide();
            $uploadContainer.show();
            $mediaId.val(message[type]['media_id']);
            $upload.attr('accept', message[type]['type']);
            $uploadTitle.text('上传文件');
            break;
        case 'textcard':
            $extra.hide();
            $mpContainer.hide();
            $title.attr('placeholder', '标题');
            $cardUrl.attr('placeholder', '链接地址');
            $title.val(message[type]['title']);
            $content.html(message[type]['description']);
            $titleContainer.show();     // title
            $contentContainer.show();   // description
            $cardUrlContainer.show();   // url
            $btnTxtContainer.show();    // btnTxt
            break;
        case 'mpnews':
            $extra.hide();
            $titleContainer.hide();
            $contentContainer.hide();
            $mpContainer.show();
            break;
        case 'sms':
            $extra.hide();
            $mpContainer.hide();
            $content.html(message[type]);
            $titleContainer.hide();
            $contentContainer.show();
            break;
        default:
            break;
    }
}).on('focus', function () {
    var type = $(this).val();
    switch (type) {
        case 'text':
            message[type]['content'] = $content.html();
            break;
        case 'image':
            message[type]['media_id'] = $mediaId.val();
            break;
        case 'voice':
            message[type]['media_id'] = $mediaId.val();
            break;
        case 'video':
            message[type] = {
                media_id: $mediaId.val(),
                title: $title.val(),
                description: $content.html()
            };
            break;
        case 'file':
            message[type]['media_id'] = $mediaId.val();
            break;
        case 'textcard':
            message[type] = {
                title: $title.val(),
                description: $content.html(),
            };
            break;
        case 'sms':
            message[type] = $content.html();
            break;
    }
});

/** 消息内容 */
// 初始化上传文件的事件
$upload.on('focus change', '#upload #mpnews-upload', function () {
    upload(this);
});
// 上传文件
$upload.on('change', function() { upload(this, false); });

/** 图文消息 */
// 上传封面图
$mpUpload.on('change', function() { upload(this, true); });
// 编辑图文
$(document).on('click', '.weui-uploader__file', function () {
    var ids = $(this).attr('id').split('-'),
        id = ids[ids.length - 1],
        news = mpnews['articles'][id];

    $mpnewsId.val(id);
    $mpTitle.val(news['title']);
    $mpContent.val(news['content']);
    $mpUrl.val(news['content_source_url']);
    $mpAuthor.val(news['author']);
    $mpDigest.val(news['digest']);
    $mpMediaId.val(news['thumb_media_id']);
    $mpUploadTitle.val(news['filename']);
    $mpFilePath.val(news['image_url']);
    $delete.show();
    $mpnews.popup();
});
// 添加图文消息
$addMpnews.on('click', function () {
    if (mpnewsCount >= 8) {
        $.alert('一条图文消息最多包含8个图文');
        return false;
    }
    $mpnewsId.val('');
    $mpTitle.val('');
    $mpMediaId.val('');
    $mpContent.val('');
    $mpAuthor.val('').attr('placeholder', '(选填)');
    $mpUrl.val('').attr('placeholder', '(选填)');
    $mpDigest.val('').attr('placeholder', '(选填)');
    $mpUploadTitle.html('封面图');
    $mpUpload.val('');
    $delete.hide();
    $('#mpnews').popup();
});
// 删除图文
$delete.on('click', function () {
    var id = $mpnewsId.val(), i = 0;

    // 从数组中移除图文
    mpnews['articles'].splice(id, 1);
    // 从图文列表中移除
    $('#mpnews-' + id).remove();
    // 重建图文索引
    $mpnewsList.find('li').each(function () {
        $(this).attr('id', '#mpnews-' + i);
        i++;
    });
    mpnewsCount--;
    $.alert('已将指定图文删除');
    $.closePopup();
});
// 保存/更新图文
$add.on('click', function () {
    var id = $mpnewsId.val(),
        title = $mpTitle.val(),
        description = $mpContent.val(),
        thumb_media_id = $mpMediaId.val(),
        image_url = $mpFilePath.val(),
        article = {
            title: title,
            thumb_media_id: thumb_media_id,
            author: $mpAuthor.val(),
            content_source_url: $mpUrl.val(),
            content: description,
            digest: $mpDigest.val(),
            filename: $mpUploadTitle.val(),
            image_url: $mpFilePath.val()
        };

    if (title === '' || description === '' || thumb_media_id === '') {
        $.alert('标题/内容/封面图不得为空');
        return false;
    }
    if (id === '') {
        mpnews['articles'].push(article);
        $mpnewsList.append(
            '<li id="mpnews-' + mpnewsCount + '" class=weui-uploader__file style="background-image:url(' + image_url + ')"></li>'
        );
        mpnewsCount += 1;
    } else {
        var $mpnews = $('#mpnews-' + id);

        mpnews['articles'][id] = article;
        $mpnews.attr('style', '"background-image:url(' + image_url + ')"');
    }
    $.closePopup();
});

/** 发送消息 */
// 初始化提交消息发送请求的事件
$send.on('click', function () {
    var departmentIds = [],
        userIds = [], mediaId,
        title, text, cardUrl, btnTxt,
        formData, content = null,
        type = $msgType.val();

    $chosenResults.find('a.department').each(function () {
        departmentIds.push($(this).attr('data-uid'));
    });
    $chosenResults.find('a.user').each(function () {
        userIds.push($(this).attr('data-uid'));
    });
    switch (type) {
        case 'text':
            content = { text: { content: $content.html() }};
            break;
        case 'image':
            mediaId = $mediaId.val();
            if (mediaId === '') { $.alert('请上传图片'); return false; }
            content = {
                image: {
                    media_id: mediaId,
                    path: $mediaId.data('path')
                }
            };
            break;
        case 'voice':
            mediaId = $mediaId.val();
            if (mediaId === '') { $.alert('请上传语音'); return false; }
            content = {
                voice: {
                    media_id: mediaId,
                    path: $mediaId.data('path')
                }
            };
            break;
        case 'video':
            title = $title.val();
            text = $content.html();
            mediaId = $mediaId.val();
            if (mediaId === '') { $.alert('请上传视频'); return false; }
            content = {
                video: {
                    media_id: mediaId,
                    title: title,
                    description: text,
                    path: $mediaId.data('path')
                }
            };
            break;
        case 'file':
            mediaId = $mediaId.val();
            if (mediaId === '') { $.alert('请上传文件'); return false; }
            content = {
                file: {
                    media_id: mediaId,
                    path: $mediaId.data('path')
                }
            };
            break;
        case 'textcard':
            title = $title.val();
            text = $content.html();
            cardUrl = $cardUrl.val();
            btnTxt = $btnTxt.val();
            if (title === '' || text === '' || cardUrl === '') {
                $.alert('标题/描述/链接地址不得为空');
                return false;
            }
            content = {
                textcard: {
                    title: title,
                    description: text,
                    url: cardUrl,
                    btntxt: btnTxt
                }
            };
            break;
        case 'mpnews':
            if (mpnews['articles'].length === 0) {
                $.alert('请添加图文');
                return false;
            }
            content = {
                mpnews: {
                    articles: mpnews['articles']
                }
            };
            break;
        case 'sms':
            text = $content.html();
            if (text.length === 0) {
                $.alert('请输入短信内容');
            }
            break;
        default:
            break;
    }
    if (userIds.length === 0 && departmentIds.length === 0) {
        $.alert('请选择发送对象');
        return false;
    }
    formData = {
        _token: wap.token(),
        type: type,
        user_ids: userIds,
        dept_ids: departmentIds,
        message_type_id: $messageTypeId.val()
    };
    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: $.extend(formData, content),
        url: 'store',
        success: function (result) {
            $.alert(result.message, function () {
                window.location.href = '../mc';
            });
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
});

/** Helper functions */
function upload(uploader, mpnews) {
    var formData = new FormData(),
        type = $msgType.val();

    formData.append('file', $(uploader)[0].files[0]);
    formData.append('_token', wap.token());
    formData.append('type', type === 'mpnews' ? 'image' : type);
    $notification.show();

    $.ajax({
        url: "create",
        data: formData,
        type: 'POST',
        dataType: 'json',
        contentType: false,
        processData: false,
        cache: false,
        success: function (result) {
            var filename = result['data']['filename'],
                mediaId = result['data']['media_id'],
                path = '../../' + result['data']['path'];

            $notification.hide();
            $(mpnews ? '#mp-upload-title' : '#upload-title').html(filename);
            $(mpnews ? '#thumb_media_id' : '#media_id').val(mediaId).attr('data-path', result['data']['path']);
            if (mpnews) {
                $mpFilePath.val(path);
            }
            $.alert(result['message']);
        },
        error: function (e) { wap.errorHandler(e); }
    });
}
function countTargets() {
    var departments = $('#chosen-results .js-chosen-results-item.department').length,
        users = $('#chosen-results .js-chosen-results-item.user').length;

    $('#count').text('已选' + departments + '个部门,' + users + '名用户');
}
function removeTarget() {
    var id = $(this).attr('data-list'),
        type = $(this).attr('data-type');
    $(this).remove();
    $('#' + type + '-' + id).find('.target-check').prop('checked', false);
    countTargets();
}
function chosenHtml(id, type, imgSrc) {
    var targetId = (type === 'department' ? 'id="department-' : 'id="user-') + id,
        imgStyle = (type === 'department' ? '' : '" style="border-radius: 50%;');

    return '<a class="chosen-results-item js-chosen-results-item ' + type + '" ' +
        targetId + '" data-list="' + id + '" data-uid="' + id + '" ' +
        'data-type="' + type + '">' +
        '<img src="' + imgSrc + imgStyle + '">' +
        '</a>';
}
function targetHtml(target, type) {
    var imgSrc = (type === 'department' ? '/img/department.png' : '/img/personal.png'),
        name = target['name'],
        imgStyle = (type === 'department' ? ' style="border-radius: 0;"' : ''),
        id = target['id'];

    return '<div style="position: relative;">' +
        '<label class="weui-cell weui-check__label" id="' + type + '-' + id +
                '" data-item="' + id + '" data-uid="' + id + '" data-type="' + type + '">' +
            '<div class="weui-cell__hd">' +
                '<input type="checkbox" class="weui-check target-check" name="targets[]" >' +
                '<i class="weui-icon-checked"></i>' +
            '</div>' +
            '<div class="weui-cell__bd">' +
                '<img src="' + imgSrc + '"' + imgStyle + ' class="js-go-detail lazy target-image" width="25" height="25">' +
                '<span class="contacts-text">' + name + '</span>' +
            '</div>' +
        '</label>' +
        (type === 'department' ? '<a class="icon iconfont icon-jiantouyou show-group targets"></a>' : '') +
    '</div>';
}