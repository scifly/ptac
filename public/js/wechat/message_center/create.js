var token = $('#csrf_token').attr('content'),
    $msgType = $('#msg-type'),
    $search = $('#search'),
    $send = $('#send'),
    $notification = $('#notification'),

    $extra = $('.extra'),   // 消息的附加属性(文件、url、图片、语音等)
    $titleContainer = $('#title-container'),
    $title = $('#title'),
    $contentContainer = $('#content-container'),
    $content = $('#content'),
    $uploadContainer = $('#upload-container'),
    $upload = $('#upload'),
    $uploadTitle = $('#upload-title'),
    $urlContainer = $('#url-container'),
    $url = $('#url'),

    $targetsContainer = $('#targets-container'),
    $targetCheck = $('.target-check'),
    $checkAll = $('#check-all'),
    $confirm = $('#confirm'),
    $chosenResults = $('#chosen-results'),

    allowedTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'], // 允许上传的图片类型
    maxSize = 1024 * 1024,  // 1024KB，也就是 1MB
    maxWidth = 300, // 图片最大宽度
    maxCount = 6,  // 最大上传图片数量
    tmp = 1,
    title = $title.val(),
    content = '',
    mediaIds = [],
    wechatMediaId = '';

// 初始化发送对象选择事件
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
getDept();
expand();
// $(".ma_expect_date").datetimePicker();

// 初始化移除发送对象的事件
$(document).on('click', '.js-chosen-results-item', function () {
    removeTarget();
});
// 初始化上传封面事件
$(document).on('change', '#mpnews-media-id', function() {
    uploadCover();
});
// 初始化开启评论的事件
$(".weui-switch").on('change', function () {
    $('.hw-time').slideToggle('fast');
});
$(".js-chosen-breadcrumb-ol li").off('click').click(function () {
    getDept(this)
});
// 初始化选择微信消息类型的事件
$msgType.on('change', function () {
    var type = $(this).val();
    $('.js-content-item input').val('');
    $content.html('');
    switch (type) {
        case 'text':
            $titleContainer.hide();
            $extra.hide();
            $contentContainer.show();
            break;
        case 'image':
            $extra.hide();
            $titleContainer.hide();
            $contentContainer.hide();
            $uploadContainer.show();
            $upload.attr('accept', 'image/*');
            $uploadTitle.text('上传图片');
            break;
        case 'voice':
            $extra.hide();
            $titleContainer.hide();
            $contentContainer.hide();
            $uploadContainer.show();
            $upload.attr('accept', 'audio/*');
            $uploadTitle.text('上传语音');
            break;
        case 'video':
            $extra.hide();
            $title.attr('placeholder', '视频标题');
            $content.attr('placeholder', '视频描述');
            $uploadTitle.text('上传视频');
            $upload.attr('accept', 'video/*');
            $titleContainer.show();     // title
            $contentContainer.show();   // description
            $uploadContainer.show();    // media_id
            break;
        case 'file':
            $extra.hide();
            $titleContainer.hide();
            $contentContainer.hide();
            $uploadContainer.show();
            $upload.attr('accept', '*');
            $uploadTitle.text('上传文件');
            break;
        case 'textcard':
            $extra.hide();
            $url.attr('placeholder', '链接地址');
            $titleContainer.show();     // title
            $contentContainer.show();   // description
            $urlContainer.show();       // url
            break;
        case 'mpnews':
            $extra.hide();
            $url.attr('placeholder', '原文链接');
            $title.attr('placeholder', '标题');
            $content.attr('placeholder', '');
            $uploadTitle.text('上传图片');
            $titleContainer.show();     // title
            $contentContainer.show();   // content
            $uploadContainer.show();    // media_ids
            break;
        case 'sms':
            $extra.hide();
            $titleContainer.hide();
            $contentContainer.show();
            break;
        default:
            break;
    }
});
// 初始化搜索发送对象的事件
$search.on("input propertychange change", function () {
    var keyword = $(this).val(), i, html = '',
        departments = function (html, result) {
            for (i = 0; i < result['gradeDepts'].length; i++) {
                html += targetHtml(result['gradeDepts'][i], 'department');
            }
            for (i = 0; i < result['classDepts'].length; i++) {
                html += targetHtml(result['classDepts'][i], 'department');
            }
            expand();
        },
        users = function (html, result) {
            for (i = 0; i < result['users'].length; i++) {
                html += targetHtml(result['users'][i]);
            }
        };

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../create',
        data: { keyword: keyword, _token: token },
        success: function (result) {
            keyword === '' ? departments(html, result) : users(html, result);
        }
    });
    $targetsContainer.html(html);
});
// 初始化确认选定发送对象的事件
$confirm.on('click', function () {
    var html = $chosenResults.html();
    $chosenResults.html(html);
    $.closePopup();
});
// 初始化选择所有发送对象的事件
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
// 初始化上传文件的事件
$upload.on('change', function (event) {
    var files = event.target.files;

    // 如果没有选中文件，直接返回
    if (files.length === 0) {
        return false;
    }

    for (var i = 0, len = files.length; i < len; i++) {
        var file = files[i],
            reader = new FileReader();

        // 如果类型不在允许的类型范围内
        if (allowedTypes.indexOf(file.type) === -1) {
            $.alert({text: '该类型不允许上传'});
            continue;
        }
        if (file.size > maxSize) {
            $.alert({text: '图片太大，不允许上传'});
            continue;
        }
        if ($('.weui_uploader_file').length >= maxCount) {
            $.alert({text: '最多只能上传' + maxCount + '张图片'});
            return;
        }
        reader.onload = function (e) {
            var img = new Image();
            img.onload = function () {
                // 不要超出最大宽度
                var w = Math.min(maxWidth, img.width);
                // 高度按比例计算
                var h = img.height * (w / img.width);
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                // 设置 canvas 的宽度和高度
                canvas.width = w;
                canvas.height = h;
                ctx.drawImage(img, 0, 0, w, h);
                var base64 = canvas.toDataURL('image/png');

                // console.log(base64);
                var html = '<img class="uploadimg-item" src="' + base64 + '" id="uploadimg-' + tmp + '" style="width: 300px;height: 187px">';
                $content.append(html);
                // 然后假装在上传，可以post base64格式，也可以构造blob对象上传，也可以用微信JSSDK上传
            };
            img.src = e.target['result'];
        };
        reader.readAsDataURL(file);
    }
    $.ajax({
        url: '../message_upload',
        type: 'POST',
        data: {
            file: $('#upload-file')[0].files[0],
            _token: token
        },
        success: function (result) {
            if (result.statusCode === 200) {
                $('#uploadimg-' + tmp).attr('data-media-id', result.message.id);
                $('#uploadimg-' + tmp).attr('src', 'http://weixin.028lk.com/' + result.message.path);
            }
        }
    });
    tmp++;
});
// 初始化上传视频的事件
$upload.on('change', function () {
    $notification.show();
    var $this = $(this);
    var formData = new FormData();
    formData.append('file', $('#upload_video')[0].files[0]);
    formData.append('_token', token);
    formData.append('type', $msgType.val());
    $.ajax({
        url: "../message_upload",
        data: formData,
        type: 'POST',
        dataType: 'json',
        contentType: false,
        processData: false,
        cache: false,
        success: function (result) {
            $('#notification').hide();
            if (result.statusCode === 1) {
                var html = '<video class="video-id" id="' + result.data.id + '" src="' + 'http://weixin.028lk.com/' + result.data.path + '" controls="controls" style="height: 200px; width: 300px"></video>' +
                    '<input id="video_media_id" name="video_media_id" value="' + result.data.media_id + '" hidden>';
                $this.parent().parent().html(html);
            } else {
                $.alert('上传失败，请稍后重新尝试！')
            }
        }
    });
});
// 初始化上传图片的事件
$uploadImage.on('change', function () {
    $('#notification').show();
    var $this = $(this);
    var formData = new FormData();
    formData.append('file', $('#upload_image')[0].files[0]);
    formData.append('_token', token);
    formData.append('type', $msgType.val());
    $.ajax({
        url: "../message_upload",
        data: formData,
        type: 'POST',
        dataType: 'json',
        contentType: false,
        processData: false,
        cache: false,
        success: function (result) {
            $('#notification').hide();
            if (result.statusCode === 1) {
                var html = '<img class="img-id" id="' + result.data.id + '" src="' + 'http://weixin.028lk.com/' + result.data.path + '" style="height: 200px; width: 300px">' +
                    '<input id="image_media_id" name="image_media_id" value="' + result.data.media_id + '" hidden>';
                $this.parent().parent().html(html);
            } else {
                $.alert('上传失败，请稍后重新尝试！')
            }
        }
    });
});

// 初始化提交消息发送请求的事件
$send.on('click', function () {
    content = $content.html();
    var departmentIds = [],
        userIds = [],
        formData,
        type = $msgType.val();

    $chosenResults.find('a.department').each(function () {
        departmentIds.push($(this).attr('data-uid'));
    });
    $chosenResults.find('a.user').each(function () {
        userIds.push($(this).attr('data-uid'));
    });
    formData = {
        _token: token,
        type: type,
        user_ids: userIds,
        dept_ids: departmentIds,

    };
    switch (type) {
        case 'video':
            content = $('#description-video').val();
            wechatMediaId = $('#video_media_id').val();
            mediaIds.push($('.video-id').attr('id'));
            if (mediaIds.length === 0 || !content) {
                $.alert('视频/描述不得为空');
                return false;
            }
            break;
        case 'image':
            content = '0';
            wechatMediaId = $('#image_media_id').val();
            mediaIds.push($('.img-id').attr('id'));
            if (mediaIds.length === 0) {
                $.alert('请上传图片');
                return false;
            }
            break;
        case 'mpnews':
            wechatMediaId = $('#mpnews_media_id').attr('data-content-id');
            break;
        case 'sms':
            title = '短信消息';
            break;
        default:
            break;
    }
    $('.uploadimg-item').each(function () {
        mediaIds.push($(this).attr('data-media-id'));
    });

    if (
        (userIds.length === 0) && (departmentIds.length === 0) ||
        !title || !content
    ) {
        $.alert('发送对象/标题/内容不得为空');
        return false;
    }
    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
            _token: token,
            title: title,
            content: content,
            department_ids: departmentIds,
            user_ids: userIds,
            media_ids: mediaIds,
            type: type,
            mediaid: wechatMediaId,
        },
        url: 'store',
        success: function (result) {
            $.alert(result.message, function () {
                window.location.href = '../message_center';
            });
        }
    });
});
function expand() {
    $(document).on('click', '.targets', function () {
        // 展示下一个分组
        $(this).unbind("click");
        var id = $(this).prev().attr('data-uid'),
            name = $(this).prev().find('span').html(),
            chosen_dept = $('.js-chosen-breadcrumb-ol'),
            html = '<li data-id="' + id + '" class="js-chosen-breadcrumb-li headclick"><a>' + name + '</a></li>';
        $.ajax({
            type: 'GET',
            data: {},
            url: 'message_dept/' + id,
            success: function (result) {
                if (result.statusCode === 200) {
                    $targetsContainer.html(result.message);
                    chosen_dept.append(html);
                    expand();
                    removeTarget();
                    getDept();
                } else {
                    $targetsContainer.empty();
                }
            }
        });
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
function getDept(obj) {
    var id = $(obj).attr("data-id");

    $(this).nextAll().remove();
    $.ajax({
        type: 'GET',
        url: 'message_dept/' + id,
        success: function (result) {
            if (result.statusCode === 200) {
                $targetsContainer.html(result.message);
                expand();
                removeTarget();
                getDept();
            } else {
                $targetsContainer.empty();
            }
        }
    });
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
    var imgSrc = (type === 'department' ? 'img/department.png' : 'img/personal.png'),
        name = (type === 'department' ? target['name'] : target['realname']),
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
                '<img src="' + imgSrc + '"' + imgStyle + ' class="js-go-detail lazy" width="75" height="75">' +
                '<span class="contacts-text">' + name + '</span>' +
            '</div>' +
        '</label>' +
        (type === 'department' ? '<a class="icon iconfont icon-jiantouyou show-group targets"></a>' : '') +
        '</div>';
}
function uploadCover() {
    $notification.show();
    $.ajax({
        url: '../message_upload',
        type: 'POST',
        cache: false,
        data: {
            file:  $uploadMpnews[0].files[0],
            _token: token,
            type: $msgType.val()
        },
        success: function (result) {
            $notification.hide();
            if (result.statusCode === 1) {
                var html =
                    '<img class="uploadimg-item upload_mpnews" id="' + result.data.id + '" ' +
                    'src="http://weixin.028lk.com/' + result.data.path + '"  ' +
                    'style="width: 100%" data-id="' + result.data.id +
                    '">' +
                    '<input id="mpnews-media-id" name="mpnews-media-id" ' +
                    'data-content-id="' + result.data.media_id + '" ' +
                    'class="weui-uploader__input" type="file" ' +
                    'accept="image/*" multiple="" ' +
                    '>';
                $cover.html(html);
            } else {
                $.alert('上传失败，请稍后重新尝试！')
            }
        }
    });
}
