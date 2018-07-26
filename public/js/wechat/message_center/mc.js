//# sourceURL=mc.js
(function ($) {
    $.mc = function (options) {
        var mc = {
            options: $.extend({}, options),
            message: {
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
            mpnews: { articles: [] },
            mpnewsCount: 0,
            index: function () {
                var $types = $('.weui-navbar__item'),
                    $messages = $('.teacher-list-box'),
                    $messageTypeContainer = $('.selectlist-layout'),
                    $selectBox = $('.select-box'),
                    $messageTypes = $('.select-ul'),
                    $messageType = $('.select-container'),
                    $search = $('#searchInput');

                // 选择消息类型
                $messageTypeContainer.on('click', function () {
                    $messageType.toggle();
                    $messageTypes.slideToggle('fast');
                });
                // 显示指定类型的消息列表
                $messageTypes.find('li').on('click', function () {
                    var html = '' + ($(this).text()) + ' <i class="icon iconfont icon-arrLeft-fill"></i>',
                        typeId = $(this).attr('data-id');

                    $messageType.toggle();
                    $messageTypes.slideToggle('fast');
                    $messageTypes.find('li').removeClass('c-green');
                    $(this).addClass('c-green');
                    $selectBox.html(html);
                    mc.messageFilter(typeId)
                });
                // 查看（已发送）或编辑（草稿/待发送）消息
                $messages.on('click', function () {
                    var $this = $(this),
                        id = $this.attr('id'),
                        type = $this.data('type');

                    if (type === 'sent') {
                        $.ajax({
                            type: 'GET',
                            dataType: 'json',
                            url: 'mc',
                            data: { _token: wap.token(), id: id},
                            success: function () {
                                window.location = 'mc/show/' + id;
                            },
                            error: function (e) {
                                wap.errorHandler(e);
                            }
                        });
                    } else {
                        window.location = 'mc/edit/' + id;
                    }
                });
                // 显示发件箱/收件箱消息列表
                $types.on('click', function() {
                    $messageTypes.hide();
                    $messageType.hide();
                });
                // 搜索消息
                $search.on("input propertychange change",
                    function() {
                        var keyword = $(this).val(),
                            type = $('.weui-bar__item--on').attr('data-type'),
                            $messageList = $('.weui-popup__container .weui-tab__bd-item .list-layout');

                        $messageList.html('');
                        $.ajax({
                            type: 'POST',
                            dataType: 'html',
                            url: 'mc',
                            data: {
                                _token: wap.token(),
                                keyword: keyword,
                                type: type
                            },
                            success: function (result) {
                                $messageList.html(result);
                            },
                            error: function (e) {
                                wap.errorHandler(e);
                            }
                        });
                    }
                );
            },
            ce: function () {
                var $id = $('#id'),
                    $mpnewsList = $('#mpnews-list'),
                    $title = $('#title'),
                    $content = $('#content'),
                    $mediaId = $('#media_id'),
                    type = $('#msg-type').val();

                if ($id.length !== 0) {
                    switch (type) {
                        case 'text':
                            mc.message[type]['content'] = $content.html();
                            break;
                        case 'image':
                        case 'voice':
                        case 'file':
                            mc.message[type]['media_id'] = $mediaId.val();
                            break;
                        case 'video':
                            mc.message[type] = {
                                media_id: $mediaId.val(),
                                title: $title.val(),
                                description: $content.html()
                            };
                            break;
                        case 'textcard':
                            mc.message[type] = {
                                title: $title.val(),
                                description: $content.html(),
                            };
                            break;
                        case 'mpnews':
                            $mpnewsList.find('li').each(function() {
                                var $article = $(this);

                                mc.mpnews['articles'].push({
                                    title: $article.data('title'),
                                    thumb_media_id: $article.data('media-id'),
                                    author: $article.data('author'),
                                    content_source_url: $article.data('url'),
                                    content: $article.data('content'),
                                    digest: $article.data('digest'),
                                    filname: $article.data('filename'),
                                    image_url: $article.data('image')
                                });
                                mc.mpnewsCount += 1;
                            });
                            break;
                        case 'sms':
                            mc.message[type] = $content.html();
                            break;
                        default:
                            break;
                    }

                }
                mc.targets();
                mc.msgType();
                mc.content();
                mc.initMpnews();
                mc.initAction();
            },
            show: function () {
                var $mslId = $('#msl_id'),
                    $id = $('#id'),
                    $delete = $('.delete-message'),
                    $showComment = $('.js-show-comment'),
                    $comment = $('#mycomment'),
                    $replyContent = $('.weui_textarea'),
                    $reply = $('.send-btn');

                // 删除消息
                $delete.on('click', function () {
                    $.confirm({
                        title: '确认删除这条信息？',
                        text: '',
                        onOK: function () {
                            //点击确认
                            $.ajax({
                                type: 'DELETE',
                                dataType: 'json',
                                url: '../delete/' + $id.val(),
                                data: { _token: wap.token() },
                                success: function (result) {
                                    $.toptip(result['message'], 'success');
                                    window.location.href = '../';
                                },
                                error: function (e) {
                                    wap.errorHandler(e);
                                }
                            });
                        }
                    });
                });
                // 打开消息评论
                $showComment.on('click', function () { $comment.popup(); });
                // 评论字数限制
                $replyContent.on("input propertychange", function () {
                    $('.weui_textarea_counter span').text($(this).val().length);
                });
                // 回复消息
                $reply.off('click').on('click', function () {
                    var content = $replyContent.val();

                    if (content.length === 0){
                        $.toptip('回复内容不能为空！');
                        return false;
                    }
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: '../show',
                        data: {
                            _token: wap.token(),
                            content: content,
                            msl_id: $mslId.val()
                        },
                        success: function (result) {
                            $.toptip(result['message'], 'success');
                            $.closePopup();
                            mc.messageReplies();
                        },
                        error: function (e) {
                            wap.errorHandler(e);
                        }
                    });
                });
                // 删除回复
                $(document).on('click', '.del-reply', function () {
                    var id = $(this).attr('id');
                    $.confirm({
                        title: '确认删除这条回复？',
                        text: '',
                        onOK: function () {
                            $.ajax({
                                type: 'DELETE',
                                dataType: 'json',
                                url: '../show',
                                data: {
                                    _token: wap.token(),
                                    id: id
                                },
                                success: function (result) {
                                    $.toptip(result['message'], 'success');
                                    mc.messageReplies();
                                },
                                error: function (e) {
                                    wap.errorHandler(e);
                                }
                            });
                        }
                    });
                });
            },
            messageReplies: function () {
                var $replies = $('.discuss_list'),
                    $id = $('#id'),
                    $mslId = $('#msl_id');

                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: '../show',
                    data: {
                        _token: wap.token(),
                        id: $id.val(),
                        msl_id: $mslId.val()
                    },
                    success: function (result) {
                        $replies.html(result);
                    },
                    error: function (e) {
                        wap.errorHandler(e);
                    }
                });
            },
            messageFilter: function (typeId) {
                if (typeId === '0') {
                    $('.table-list').show();
                } else {
                    $('.table-list').hide();
                    $('.list-' + typeId).show();
                }
            },
            targets: function () {
                var $targetsContainer = $('#targets-container'),
                    $checkAll = $('#check-all'),
                    $confirm = $('#confirm'),
                    $chosenTargets = $('#chosen-results'),
                    $search = $('#search'),
                    $back = $('#back');

                // 初始化确认选定发送对象的事件
                $confirm.on('click', function () {
                    var html = $chosenTargets.html();
                    $chosenTargets.html(html);
                    $.closePopup();
                });
                // 选择所有发送对象
                $checkAll.on('click', function () {
                    if ($(this).is(':checked')) {
                        var html = '';

                        $('.target-check').prop('checked', true);
                        $('.js-chosen-items .weui-check__label').each(
                            function (i, target) {
                                var $target = $(target),
                                    type = $target.data('type'),
                                    id = $target.data('item'),
                                    imgSrc = $target.find('img').attr('src');

                                html += mc.chosenHtml(id, type, imgSrc);
                            }
                        );
                        $chosenTargets.html(html);
                        mc.removeTarget();
                        $targetsContainer.addClass('air-checkall');
                        mc.countTargets();
                    } else {
                        $('.target-check').prop('checked', false);
                        $chosenTargets.html('');
                        $targetsContainer.removeClass('air-checkall');
                        mc.countTargets();
                    }
                });
                // 选择单个发送对象
                $(document).on('change', '.target-check',
                    function () {
                        var $this = $(this).parents('.weui-check__label'),
                            id = $this.attr('data-item'),
                            type = $this.attr('data-type'),
                            html = '';

                        if ($(this).is(':checked')) {
                            var imgSrc = $this.find('img').attr('src');

                            html += mc.chosenHtml(id, type, imgSrc);
                            $chosenTargets.prepend(html);
                        } else {
                            $chosenTargets.find('#' + type + '-' + id).remove();
                            $targetsContainer.removeClass('air-checkall');
                            $checkAll.prop('checked', false);
                            mc.removeTarget();
                        }
                        mc.countTargets();
                    }
                );
                // 移除发送对象
                $(document).on('click', '.js-chosen-results-item',
                    function () { mc.removeTarget(); }
                );
                // 搜索发送对象
                $search.on("input propertychange change", function () {
                    var data = { keyword: $(this).val() },
                        type = $('.targets').length !== 0 ? 'department' : 'user';

                    if (type === 'user') {
                        data = $.extend(data, { deptId: $('#deptId').val() })
                    }
                    mc.targetFilter(data, type);
                });
                // 返回部门列表
                $back.on('click', function () { mc.targetFilter({}, 'list'); });
                // 显示指定部门的用户(学生、教职员工)列表
                $(document).on('click', '.targets', function () {
                    var ids = $(this).prev().attr('id').split('-');

                    mc.targetFilter({departmentId: ids[ids.length - 1]}, 'user');
                });
            },
            msgType: function () {
                var $title = $('#title'),
                    $content = $('#content'),
                    $extra = $('.extra'),   // 消息的附加属性(文件、url、图片、语音等)
                    $titleContainer = $('#title-container'),
                    $mpContainer = $('#mpnews-container'),
                    $contentContainer = $('#content-container'),
                    $uploadContainer = $('#upload-container'),
                    $mediaId = $('#media_id'),
                    $upload = $('#upload'),
                    $uploadTitle = $('#upload-title'),
                    $cardUrl = $('#card-url'),
                    $cardUrlContainer = $('#card-url-container'),
                    $btnTxtContainer = $('#btn-txt-container');

                $('#msg-type').on('change', function () {
                    var type = $(this).val();
                    $('.js-content-item input').val('');
                    $content.html('');
                    switch (type) {
                        case 'text':
                            $extra.hide();
                            $titleContainer.hide();
                            $mpContainer.hide();
                            $contentContainer.show();
                            $content.attr('placeholder', '请输入内容').val(mc.message[type]['content']);
                            break;
                        case 'image':
                            $extra.hide();
                            $titleContainer.hide();
                            $contentContainer.hide();
                            $mpContainer.hide();
                            $uploadContainer.show();
                            $mediaId.val(mc.message[type]['media_id']);
                            $upload.attr('accept', 'image/*');
                            $uploadTitle.text('上传图片');
                            break;
                        case 'voice':
                            $extra.hide();
                            $titleContainer.hide();
                            $contentContainer.hide();
                            $mpContainer.hide();
                            $uploadContainer.show();
                            $mediaId.val(mc.message[type]['media_id']);
                            $upload.attr('accept', 'audio/*');
                            $uploadTitle.text('上传语音');
                            break;
                        case 'video':
                            $extra.hide();
                            $mpContainer.hide();
                            $uploadTitle.text('上传视频');
                            $title.attr('placeholder', '视频标题').val(mc.message[type]['title']);
                            $content.attr('placeholder', '视频描述').val(mc.message[type]['description']);
                            $upload.attr('accept', mc.message[type]['type']);
                            $mediaId.val(mc.message[type]['media_id']);
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
                            $mediaId.val(mc.message[type]['media_id']);
                            $upload.attr('accept', mc.message[type]['type']);
                            $uploadTitle.text('上传文件');
                            break;
                        case 'textcard':
                            $extra.hide();
                            $mpContainer.hide();
                            $title.attr('placeholder', '标题');
                            $cardUrl.attr('placeholder', '链接地址');
                            $title.val(mc.message[type]['title']);
                            $content.attr('placeholder', '描述').val(mc.message[type]['description']);
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
                            $content.attr('placeholder', '短信内容').val(mc.message[type]);
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
                            mc.message[type]['content'] = $content.html();
                            break;
                        case 'image':
                        case 'voice':
                        case 'file':
                            mc.message[type]['media_id'] = $mediaId.val();
                            break;
                        case 'video':
                            mc.message[type] = {
                                media_id: $mediaId.val(),
                                title: $title.val(),
                                description: $content.html()
                            };
                            break;
                        case 'textcard':
                            mc.message[type] = {
                                title: $title.val(),
                                description: $content.html(),
                            };
                            break;
                        case 'sms':
                            mc.message[type] = $content.html();
                            break;
                    }
                });

            },
            content: function () {
                var $upload = $('#upload');

                // 初始化上传文件的事件
                $upload.on('focus change', '#upload #mpnews-upload', function () {
                    mc.upload(this);
                });
                // 上传文件
                $upload.on('change', function() { mc.upload(this, false); });
            },
            initMpnews: function () {
                var $mpnews = $('#mpnews'),
                    $addMpnews = $('#add-mpnews'),
                    $mpUpload = $('#mpnews-upload'),
                    $mpnewsId = $('#mpnews-id'),
                    $mpTitle = $('#mpnews-title'),
                    $mpContent = $('#mpnews-content'),
                    $mpUrl = $('#content-source-url'),
                    $mpAuthor = $('#author'),
                    $mpDigest = $('#digest'),
                    $mpUploadTitle = $('#mp-upload-title'),
                    $mpFilePath = $('#mp-file-path'),
                    $mpMediaId = $('#thumb_media_id'),
                    $add = $('#add'),
                    $delete = $('#delete'),
                    $mpnewsList = $('#mpnews-list');

                // 上传封面图
                $mpUpload.on('change', function() { mc.upload(this, true); });
                // 编辑图文
                $(document).on('click', '.weui-uploader__file', function () {
                    var ids = $(this).attr('id').split('-'),
                        id = ids[ids.length - 1],
                        news = mc.mpnews['articles'][id];

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
                    if (mc.mpnewsCount >= 8) {
                        $.toptip('一条图文消息最多包含8个图文', 'warning');
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
                    $mpnews.popup();
                });
                // 删除图文
                $delete.on('click', function () {
                    var id = $mpnewsId.val(), i = 0;

                    // 从数组中移除图文
                    mc.mpnews['articles'].splice(id, 1);
                    // 从图文列表中移除
                    $('#mpnews-' + id).remove();
                    // 重建图文索引
                    $mpnewsList.find('li').each(function () {
                        $(this).attr('id', '#mpnews-' + i);
                        i++;
                    });
                    mc.mpnewsCount--;
                    $.toptip('已将指定图文删除', 'success');
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
                        $.toptip('标题/内容/封面图不得为空', 'error');
                        return false;
                    }
                    if (id === '') {
                        mc.mpnews['articles'].push(article);
                        $mpnewsList.append(
                            '<li id="mpnews-' + mc.mpnewsCount + '" ' +
                            'class=weui-uploader__file ' +
                            'style="background-image: url(' + image_url + ')">' +
                            '</li>'
                        );
                        mc.mpnewsCount += 1;
                    } else {
                        var $mpnews = $('#mpnews-' + id);

                        mc.mpnews['articles'][id] = article;
                        $mpnews.attr('style', '"background-image:url(' + image_url + ')"');
                    }
                    $.closePopup();
                });
            },
            initAction: function () {
                var $send = $('#send'),
                    $draft = $('#draft');

                $send.on('click', function () { mc.action('send'); });
                $draft.on('click', function () { mc.action('draft'); });
            },
            targetFilter: function (data, type) {
                var $id = $('#id'),
                    $back = $('#back'),
                    $targetsContainer = $('#targets-container'),
                    uri = $id.length === 0 ? 'create' : '../edit/' + $id.val(),
                    formData = $.extend({
                        _token: wap.token(),
                        target: type,
                    }, data);

                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: uri,
                    data: formData,
                    success: function (result) {
                        if (type === 'user') {
                            $back.show();
                            $('#deptId').val(formData['departmentId']);
                        } else {
                            $back.hide();
                        }
                        $targetsContainer.html(result);
                    },
                    error: function (e) {
                        wap.errorHandler(e);
                    }
                });
            },
            action: function (type) {
                var uri = 'send', $id = $('#id'),
                    requestType = 'POST',
                    formData = mc.data();

                switch (type) {
                    case 'send':
                        break;
                    case 'draft':
                        uri = $id.length > 0 ? '../update/' + $id.val() : 'store';
                        requestType = $id.length > 0 ? 'PUT' : 'POST';
                        break;
                    case 'schedule':
                        break;
                    default:
                        break;
                }
                if (formData !== false) {
                    $.ajax({
                        type: requestType,
                        dataType: 'json',
                        data: formData,
                        url: uri,
                        success: function (result) {
                            $.toptip(result['message'], 'success');
                        },
                        error: function (e) {
                            wap.errorHandler(e);
                        }
                    });
                }
            },
            data: function () {
                var $title = $('#title'),
                    $msgType = $('#msg-type'),
                    $chosenTargets = $('#chosen-results'),
                    $content = $('#content'),
                    $mediaId = $('#media_id'),
                    $cardUrl = $('#card-url'),
                    $btnTxt = $('#btn-txt'),
                    $messageTypeId = $('#message_type_id'),
                    departmentIds = [],
                    userIds = [], mediaId,
                    title, text, cardUrl, btnTxt,
                    formData, content = null,
                    type = $msgType.val();

                $chosenTargets.find('a.department').each(function () {
                    departmentIds.push($(this).data('uid'));
                });
                $chosenTargets.find('a.user').each(function () {
                    userIds.push($(this).data('uid'));
                });
                switch (type) {
                    case 'text':
                        if ($content.html() === '') {
                            $.toptip('请输入消息内容');
                            return false;
                        }
                        content = { text: { content: $content.html() }};
                        break;
                    case 'image':
                        mediaId = $mediaId.val();
                        if (mediaId === '') {
                            $.toptip('请上传图片');
                            return false;
                        }
                        content = {
                            image: {
                                media_id: mediaId,
                                path: $mediaId.data('path')
                            }
                        };
                        break;
                    case 'voice':
                        mediaId = $mediaId.val();
                        if (mediaId === '') {
                            $.toptip('请上传语音', 'error');
                            return false;
                        }
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
                        if (mediaId === '') {
                            $.toptip('请上传视频', 'error');
                            return false;
                        }
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
                        if (mediaId === '') {
                            $.toptip('请上传文件', 'error');
                            return false;
                        }
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
                            $.toptip('标题/描述/链接地址不得为空', 'error');
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
                        if (mc.mpnews['articles'].length === 0) {
                            $.toptip('请添加图文', 'error');
                            return false;
                        }
                        content = {
                            mpnews: {
                                articles: mc.mpnews['articles']
                            }
                        };
                        break;
                    case 'sms':
                        text = $content.html();
                        if (text.length === 0) {
                            $.toptip('请输入短信内容', 'error');
                        }
                        content = { sms: text };
                        break;
                    default:
                        break;
                }
                if (userIds.length === 0 && departmentIds.length === 0) {
                    $.toptip('请选择发送对象', 'error');
                    return false;
                }
                formData = {
                    _token: wap.token(),
                    type: type,
                    user_ids: userIds,
                    dept_ids: departmentIds,
                    message_type_id: $messageTypeId.val()
                };

                return $.extend(formData, content);
            },
            upload: function (uploader, mpnews) {
                var $notification = $('#notification'),
                    $msgType = $('#msg-type'),
                    $mpFilePath = $('#mp-file-path'),
                    formData = new FormData(),
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
                        var $display = $('#file-display'),
                            filename = result['data']['filename'],
                            mediaId = result['data']['media_id'],
                            path = '../../' + result['data']['path'];

                        $notification.hide();
                        $(mpnews ? '#mp-upload-title' : '#upload-title').html(filename);
                        $(mpnews ? '#thumb_media_id' : '#media_id').val(mediaId).attr('data-path', path);
                        $display.show()
                            .find('li:first-child')
                            .attr('style', 'background-image:url(/' + (type === 'image' ? path : 'img/0.png') + ')');
                        if (mpnews) {
                            $mpFilePath.val(path);
                        }
                        $.toptip(result['message'], 'success');
                    },
                    error: function (e) { wap.errorHandler(e); }
                });
            },
            removeTarget: function () {
                var id = $(this).attr('data-list'),
                    type = $(this).attr('data-type');
                $(this).remove();
                $('#' + type + '-' + id).find('.target-check').prop('checked', false);
                mc.countTargets();
            },
            countTargets: function () {
                var departments = $('#chosen-results .js-chosen-results-item.department').length,
                    users = $('#chosen-results .js-chosen-results-item.user').length;

                $('#count').text('已选' + departments + '个部门,' + users + '名用户');
            },
            chosenHtml: function (id, type, imgSrc) {
                var targetId = (type === 'department' ? 'id="department-' : 'id="user-') + id,
                    imgStyle = (type === 'department' ? '' : '" style="border-radius: 50%;');

                return '<a class="chosen-results-item js-chosen-results-item ' + type + '" ' +
                    targetId + '" data-list="' + id + '" data-uid="' + id + '" ' +
                    'data-type="' + type + '">' +
                    '<img src="' + imgSrc + imgStyle + '">' +
                    '</a>';
            },
        };

        return {
            index: mc.index,
            ce: mc.ce,
            show: mc.show
        }
    }
})(jQuery);