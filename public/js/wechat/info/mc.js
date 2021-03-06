//# sourceURL=mc.js
(function ($) {
    $.mc = function (options) {
        var mc = {
            options: $.extend({}, options),
            message: {
                text: { content: '' },
                image: {
                    media_id: '', filename: '', path: '',
                    accept: 'image/*', uploadTitle: '上传图片'
                },
                voice: {
                    media_id: '', filename: '', path: '',
                    accept: 'audio/*', uploadTitle: '上传语音'
                },
                file: {
                    media_id: '', filename: '', path: '',
                    accept: '*',  uploadTitle: '上传文件'
                },
                video: {
                    media_id: '', filename: '', path: '', uploadTitle: '上传视频',
                    accept: 'video/mp4', title: '', description: '',
                },
                textcard: {
                    title: '', description: '', url: '', btntxt: ''
                },
                sms: ''
            },
            mpnews: { articles: [] },
            mpnewsCount: 0,
            selectedDepartmentIds: [],
            selectedUserIds: [],
            index: function () {
                var $app = $('#app'),
                    $search = $('#search'),
                    $start = $('#start'),
                    $end = $('#end'),
                    $title = $('.weui-panel__hd'),
                    $empty = $('#empty'),
                    $loadmore = $('#loader'),
                    $page = $('#page'),
                    $folder = $('#folder'),
                    $msgList = $('#msg_list'),
                    $filter = $('#filter'),
                    $messasgeTypeId = $('#message_type_id'),
                    $mediaTypeId = $('#media_type_id'),
                    titles = {
                        all: { text: '所有消息', color: 'color-primary' },
                        inbox: { text: '收件箱', color: 'color-primary' },
                        outbox: { text: '发件箱', color: 'color-warning' },
                        draft: { text: '草稿箱', color: 'color-danger' }
                    };

                $start.calendar();
                $end.calendar();
                // $page.val(1);
                action('folder');
                // 搜索
                $search.on("input propertychange change", function () {
                    $page.val(1);
                    action('search');
                });
                // 滚动
                $app.on('scroll', function () {
                    if ($app.scrollTop() + $(window).height() >= $app.prop('scrollHeight')) {
                        $loadmore.show();
                        messages('page', function (result) {
                            var st = $app.scrollTop();
                            if (result !== '') {
                                $empty.hide();
                                $msgList.append(result);
                                $page.val(parseInt($page.val()) + 1);
                            }
                            $loadmore.hide();
                            $app.scrollTop(st - 1);
                        });
                    }
                });
                // 目录
                $(document).on('click', '#show-actions', function () {
                    var actions = [];
                    $.each(titles, function (key, value) {
                        actions.push({
                            text: value['text'],
                            className: value['color'],
                            onClick: function() { switchFolder(this, key); }
                        });
                    });
                    actions.push({
                        text: '消息过滤',
                        onClick: function () { $('#filters').popup(); }
                    });
                    $.actions({
                        // title: '请选择',
                        onClose: function () { console.log('close'); },
                        actions: actions
                    });
                });
                // 过滤
                $filter.on('click', function () {
                    $page.val(1);
                    action('filter')
                });
                // 查看/编辑
                $(document).on('click', '.weui-cell_access', function (e) {
                    e.preventDefault(e);
                    var $this = $(this),
                        id = $this.attr('id'),
                        sent = parseInt($this.data('type')),
                        location = (sent ? 'show/' : 'edit/') + id;

                    sent ? $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        url: 'index',
                        data: {_token: wap.token(), id: id},
                        success: function () {
                            window.location = location;
                        },
                        error: function (e) {
                            wap.errorHandler(e);
                        }
                    }) : window.location = location;
                });
                function switchFolder(o, folder) {
                    $title.html(o.text)
                        .removeClass($title.attr('class').split(' ')[1])
                        .addClass(o.className);
                    $folder.val(folder);
                    $page.val(1);
                    action('folder');
                }
                function messages(action, callback) {
                    $loadmore.show();
                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        url: 'index',
                        data: data(action),
                        success: function (result) {
                            callback(result);
                        },
                        error: function (e) {
                            wap.errorHandler(e);
                        }
                    });
                }
                function data(action) {
                    return {
                        _token: wap.token(),
                        action: action,
                        params: {
                            keyword: $search.val(),
                            page: $page.val(),
                            folder: $folder.val(),
                            message_type_id: $messasgeTypeId.val(),
                            media_type_id: $mediaTypeId.val(),
                            start: $start.val(),
                            end: $end.val()
                        }
                    }
                }
                function action(action) {
                    if ($page.val() === '') {
                        $page.val(1);
                        $folder.val('all');
                        $title.text('所有消息');
                        $title.addClass(titles['all']['color'])
                    } else {
                        var folder= titles[$folder.val()],
                            classes = $title.attr('class').split,
                            color = classes.length > 1 ? classes[1] : '';

                        $title.text(folder['text'])
                            .removeClass(color)
                            .addClass(folder['color']);
                    }
                    $msgList.hide();
                    messages(action, function (result) {
                        $loadmore.hide();
                        $msgList.html(result).show();
                        $empty.toggle($msgList.html() === '');
                    });
                }
            },
            ce: function () {
                // 创建或编辑消息（草稿）
                var $id = $('#id'),
                    $chosenTargets = $('#chosen-results'),
                    $checkAll = $('#check-all'),
                    type = $('#msg-type').val();

                mc.selectedDepartmentIds = [];
                mc.selectedUserIds = [];
                $checkAll.prop('checked', false);

                if ($id.length !== 0) {
                    // 将需要编辑的消息放入内存
                    mc.store(type);
                    // 将需要编辑的消息对应的发送对象放入内存
                    $chosenTargets.find('a').each(
                        function (i, target) {
                            var $target = $(target),
                                id = $target.data('uid'),
                                type = $target.data('type');
                            type === 'user' ? mc.selectedUserIds.push(id) : mc.selectedDepartmentIds.push(id);
                        }
                    );
                    $checkAll.prop('checked', mc.allChecked());
                }
                mc.targets();
                mc.msgType();
                mc.content();
                mc.initMpnews();
                mc.initAction();
                mc.timing();
            },
            show: function () {
                var $mslId = $('input[name=message_log_id]'),
                    $id = $('input[name=id]'),
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
                            message_log_id: $mslId.val()
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
                    $id = $('input[name=id]'),
                    $mslId = $('input[name=message_log_id]');

                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: '../show',
                    data: {
                        _token: wap.token(),
                        id: $id.val(),
                        message_log_id: $mslId.val()
                    },
                    success: function (result) {
                        $replies.html(result);
                    },
                    error: function (e) {
                        wap.errorHandler(e);
                    }
                });
            },
            targets: function () {
                var $checkAll = $('#check-all'),
                    $confirm = $('#confirm'),
                    $search = $('#search'),
                    $back = $('#back');

                // 初始化确认选定发送对象的事件
                $confirm.on('click', function () { $.closePopup(); });
                // 选择所有发送对象
                $checkAll.on('click', function () { mc.checkAll(!!$(this).is(':checked')); });
                // 选择单个发送对象
                $(document).on('change', '.target-check',
                    function () {
                        var $this = $(this).parents('.weui-check__label'),
                            id = $this.data('uid'),
                            type = $this.data('type');
                        $(this).is(':checked') ? mc.addTarget(id, type) : mc.removeTarget(id, type);
                    }
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
                $back.on('click', function () {
                    mc.targetFilter({}, 'list');
                });
                // 显示指定部门的用户(学生、教职员工)列表
                $(document).on('click', '.targets', function () {
                    var ids = $(this).prev().attr('id').split('-');

                    mc.targetFilter({departmentId: ids[ids.length - 1]}, 'user');
                });
            },
            msgType: function () {
                var $title = $('#title'),
                    $content = $('#content'),
                    $mediaId = $('#media_id'),
                    $upload = $('#upload'),
                    $uploadTitle = $('#upload-title'),
                    $cardUrl = $('#card-url'),
                    $btnTxt = $('#btn-txt'),
                    $mpnewsList = $('#mpnews-list');

                $('#msg-type').on('change', function () {
                    var type = $(this).val(),
                        message = mc.message[type];

                    $title.parents('.weui-cell').toggle(
                        $.inArray(type, ['video', 'textcard']) !== -1
                    );
                    $content.parents('.weui-cell').toggle(
                        $.inArray(type, ['text', 'video', 'textcard', 'sms']) !== -1
                    );
                    $upload.parents('.weui-cell').toggle(
                        $.inArray(type, ['image', 'voice', 'video', 'file']) !== -1
                    );
                    $cardUrl.parents('.weui-cell').toggle(
                        type === 'textcard'
                    );
                    $btnTxt.parents('.weui-cell').toggle(
                        type === 'textcard'
                    );
                    $mpnewsList.parents('.weui-cell').toggle(
                        type === 'mpnews'
                    );
                    switch (type) {
                        case 'text':
                            $content.attr('placeholder', '请输入内容').val(message['content']);
                            break;
                        case 'image':
                        case 'voice':
                        case 'file':
                        case 'video':
                            $mediaId.val(message['media_id']).attr('data-path', message['path']);
                            $uploadTitle.text(
                                message['media_id'] === '' ? message['uploadTitle'] : message['filename']
                            );
                            $upload.attr('accept', message['accept']);
                            if (type === 'video') {
                                $title.attr('placeholder', '视频标题').val(message['title']);
                                $content.attr('placeholder', '视频描述').val(message['description']);
                            } else {
                                $('#file-display').toggle(message['media_id'] !== '');
                            }
                            break;
                        case 'textcard':
                            $title.attr('placeholder', '标题').val(message['title']);
                            $cardUrl.attr('placeholder', '链接地址').val(message['url']);
                            $content.attr('placeholder', '描述').val(message['description']);
                            $btnTxt.attr('placeholder', '按钮文字').val(message['btntxt']);
                            break;
                        case 'sms':
                            $content.attr('placeholder', '短信内容').val(message);
                            break;
                        default:
                            break;
                    }
                }).on('focus', function () {
                    mc.store($(this).val());
                });

            },
            content: function () {
                var $upload = $('#upload');

                // 初始化上传文件的事件
                $upload.on('focus change', '#upload #mpnews-upload',
                    function () { mc.upload(this); }
                );
                // 上传文件
                $upload.on('change', function() { mc.upload(this, false); });
            },
            initMpnews: function () {
                // 初始化图文消息相关事件与功能
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

                // 打开新增图文消息的弹窗
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
                    $('#cover-image').hide();
                    $mpnews.popup();
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
                            filename: $mpUploadTitle.html(),
                            image_url: $mpFilePath.val()
                        };

                    if (title === '' || description === '' || thumb_media_id === '') {
                        $.toptip('标题/内容/封面图不得为空', 'error');
                        return false;
                    }
                    if (id === '') {
                        mc.mpnews['articles'].push(article);
                        var url = '/' + article['image_url'];
                        $mpnewsList.append(
                            '<li id="mpnews-' + mc.mpnewsCount + '" ' +
                            'class="weui-uploader__file" ' +
                            'style="background-image: url(' + url + ')" ' +
                            'data-media-id="' + article['thumb_media_id'] + '" ' +
                            'data-author="' + article['author'] + '" ' +
                            'data-content="' + article['content'] + '" ' +
                            'data-digest="' + article['digest'] + '" ' +
                            'data-filename="' + article['filename'] + '" ' +
                            'data-url="' + article['content_source_url'] + '" ' +
                            'data-image="' + article['image_url'] + '" ' +
                            'data-title="' + article['title'] + '">' +
                            '</li>'
                        );
                        mc.mpnewsCount += 1;
                    } else {
                        var $mpnews = $('#mpnews-' + id);

                        mc.mpnews['articles'][id] = article;
                        $mpnews.attr('style', 'background-image:url(/' + image_url + ')');
                    }
                    $.closePopup();
                });
                // 编辑图文
                $(document).on('click', '.weui-uploader__file',
                    function () {
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
                        $mpUploadTitle.html(news['filename']);
                        $mpFilePath.val(news['image_url']);
                        $delete.show();
                        $('#cover-image').show().find('li:first-child')
                            .attr('style', 'background-image:url(/' + news['image_url'] + ')');
                        $mpnews.popup();
                    }
                );
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
                // 上传封面图
                $mpUpload.on('change', function() { mc.upload(this, true); });
            },
            initAction: function () {
                var $send = $('#send'),
                    $draft = $('#draft');

                $send.on('click', function () { mc.action('send'); });
                $draft.on('click', function () { mc.action('draft'); });
            },
            targetFilter: function (data, type) {
                var $id = $('#id'),
                    $notification = $('#notification'),
                    $back = $('#back'),
                    $targetsContainer = $('#targets-container'),
                    uri = $id.length === 0 ? 'create' : '../edit/' + $id.val(),
                    formData = $.extend({
                        _token: wap.token(),
                        target: type,
                    }, data);

                $notification.show();
                $.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: uri,
                    data: formData,
                    success: function (result) {
                        var checkAll = true;

                        $notification.hide();
                        $back.toggle(type === 'user');
                        if (type === 'user') {
                            $('#deptId').val(formData['departmentId']);
                        }
                        $targetsContainer
                            .attr('style', result !== '' ? '' : 'text-align: center;')
                            .html(result !== '' ? result : '暂无数据');
                        if (result !== '') {
                            $targetsContainer.find('label').each(
                                function (i, target) {
                                    var $target = $(target),
                                        id = $target.data('uid');
                                    if (
                                        $.inArray(id, mc.selectedDepartmentIds) !== -1 ||
                                        $.inArray(id, mc.selectedUserIds) !== -1
                                    ) {
                                        $target.find('input').prop('checked', true);
                                    } else {
                                        checkAll = false;
                                    }
                                }
                            );
                            $('#check-all').prop('checked', checkAll);
                        }
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
                var $id = $('#id'),
                    $title = $('#title'),
                    $msgType = $('#msg-type'),
                    $content = $('#content'),
                    $mediaId = $('#media_id'),
                    $cardUrl = $('#card-url'),
                    $btnTxt = $('#btn-txt'),
                    $timing = $('#timing'), $time = $('#time'),
                    title, text, cardUrl, btnTxt,
                    formData, content = {},
                    type = $msgType.val(),
                    fileTypes = {
                        image: '图片',
                        voice: '语音',
                        video: '视频',
                        file: '文件'
                    };

                // 判断是否选择了发送对象（部门、会员）
                if (
                    mc.selectedDepartmentIds.length === 0 &&
                    mc.selectedUserIds.length === 0
                ) {
                    $.toptip('请选择发送对象', 'error');
                    return false;
                }
                switch (type) {
                    case 'text':
                        if ($content.val() === '') {
                            $.toptip('请输入文本内容');
                            return false;
                        }
                        content = { text: { content: $content.val() }};
                        break;
                    case 'image':
                    case 'voice':
                    case 'video':
                    case 'file':
                        content[type] = {
                            media_id: $mediaId.val(),
                            path: $mediaId.data('path')
                        };
                        if (type === 'video') {
                            content[type]['title'] = $title.val();
                            content[type]['description'] = $content.val();
                            if ($title.val() === '') {
                                $.toptip('请输入视频标题', 'error');
                                return false;
                            }
                        }
                        if ($mediaId.val() === '') {
                            $.toptip('请上传需要发送的' + fileTypes[type]);
                        }
                        break;
                    case 'textcard':
                        title = $title.val();
                        text = $content.val();
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
                            $.toptip('请添加至少一个图文', 'error');
                            return false;
                        }
                        content = {
                            mpnews: {
                                articles: mc.mpnews['articles']
                            }
                        };
                        break;
                    case 'sms':
                        text = $content.val();
                        if (text.length === 0) {
                            $.toptip('请输入短信内容', 'error');
                            return false;
                        }
                        content = { sms: text };
                        break;
                    default:
                        break;
                }
                formData = {
                    _token: wap.token(),
                    type: type,
                    user_ids: mc.selectedUserIds,
                    dept_ids: mc.selectedDepartmentIds,
                    message_type_id: $('#message_type_id').val()
                };
                if ($timing.val() === '1') {
                    $.extend(formData, { time: $time.val() });
                }
                if ($id.length > 0) {
                    $.extend(formData, { id: $id.val() });
                }

                return $.extend(formData, content);
            },
            upload: function (uploader, mpnews) {
                var $id = $('#id'),
                    uri = $id.length === 0 ? 'create' : '../edit/' + $id.val(),
                    $notification = $('#notification'),
                    $mpFilePath = $('#mp-file-path'),
                    formData = new FormData(),
                    type = $('#msg-type').val();

                formData.append('file', $(uploader)[0].files[0]);
                formData.append('_token', wap.token());
                formData.append('type', type === 'mpnews' ? 'image' : type);
                $notification.show();

                $.ajax({
                    url: uri,
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
                        if (!mpnews) {
                            mc.message[type]['filename'] = filename;
                            mc.message[type]['path'] = path;
                            $display.show().find('li:first-child')
                                .attr('style', 'background-image:url(/' + (type === 'image' ? path : 'img/0.png') + ')');
                        } else {
                            $('#cover-image').show().find('li:first-child')
                                .attr('style', 'background-image:url(/' + path + ')');
                            $mpFilePath.val(path);
                        }
                        $.toptip(result['message'], 'success');
                    },
                    error: function (e) { wap.errorHandler(e); }
                });
            },
            countTargets: function () {
                $('#count').text(
                    '已选' + mc.selectedDepartmentIds.length + '个部门,' + mc.selectedUserIds.length + '名用户'
                );
            },
            chosenHtml: function (id, type) {
                var targetId = (type === 'user' ? 'user-' : 'department-') + id,
                    style = (type === 'user' ? 'border-radius: 50%;' : ''),
                    src = '/img/' + (type === 'user' ? 'personal.png' : 'department.png');

                return '<a id="' + targetId + '" class="chosen-results-item" data-uid="' + id +
                    '" data-type="' + type + '">' +
                    '<img src="' + src + '" style="' + style + '" alt="" />' +
                    '</a>';
            },
            addTarget: function (id, type) {
                if (type === 'user') {
                    if ($.inArray(id, mc.selectedUserIds) === -1) {
                        mc.selectedUserIds.push(id);
                    }
                } else {
                    if ($.inArray(id, mc.selectedDepartmentIds) === -1) {
                        mc.selectedDepartmentIds.push(id);
                    }
                }
                $('#check-all').prop('checked', mc.allChecked());
                mc.refreshTargets();
            },
            removeTarget: function (id, type) {
                if (type === 'user') {
                    if ($.inArray(id, mc.selectedUserIds) !== -1) {
                        mc.selectedUserIds.splice(
                            $.inArray(id, mc.selectedUserIds), 1
                        );
                    }
                } else {
                    if ($.inArray(id, mc.selectedDepartmentIds) !== -1) {
                        mc.selectedDepartmentIds.splice(
                            $.inArray(id, mc.selectedDepartmentIds), 1
                        );
                    }
                }
                $('#check-all').prop('checked', false);
                mc.refreshTargets();
            },
            checkAll: function (checked) {
                // 全选/取消全选当前发送对象列表中的所有部门/用户
                var $targetsContainer = $('#targets-container');

                $('.target-check').prop('checked', checked);
                $('#check-all').prop('checked', checked);
                $targetsContainer.find('label').each(
                    function (i, target) {
                        var $target = $(target),
                            id = $target.data('uid'),
                            type = $target.data('type');

                        checked ? mc.addTarget(id, type) : mc.removeTarget(id, type)
                    }
                );
            },
            refreshTargets: function () {
                var targets = [
                    { ids: mc.selectedDepartmentIds, type: 'department'},
                    { ids: mc.selectedUserIds, type: 'user' }
                ];
                $('#chosen-results').html(
                    targets.map(function (target) {
                        var html = '', type = target['type'];
                        $.each(target['ids'], function () {
                            html += mc.chosenHtml(this, type);
                        });
                        return html;
                    }).join('')
                );
                mc.countTargets();
            },
            store: function (type) {
                // 将当前需要发送或编辑的消息放入内存
                var $title = $('#title'),
                    $content = $('#content'),
                    $mpnewsList = $('#mpnews-list'),
                    $mediaId = $('#media_id'),
                    $uploadTitle = $('#upload-title');

                switch (type) {
                    case 'text':
                        mc.message[type]['content'] = $content.val();
                        break;
                    case 'image':
                    case 'voice':
                    case 'file':
                    case 'video':
                        mc.message[type] = {
                            media_id: $mediaId.val(),
                            filename: $uploadTitle.val(),
                            path: $mediaId.data('path')
                        };
                        if (type === 'video') {
                            mc.message[type]['title'] = $title.val();
                            mc.message[type]['description'] = $content.val();
                        }
                        break;
                    case 'textcard':
                        mc.message[type] = {
                            title: $title.val(),
                            description: $content.val(),
                            url: $contentSourceUrl.val(),
                            btntxt: $cardBtntxt.val()
                        };
                        break;
                    case 'mpnews':
                        mc.mpnewsCount = 0;
                        $mpnewsList.find('li').each(function() {
                            var $article = $(this);

                            mc.mpnews['articles'].push({
                                title: $article.data('title'),
                                thumb_media_id: $article.data('media-id'),
                                author: $article.data('author'),
                                content_source_url: $article.data('url'),
                                content: $article.data('content'),
                                digest: $article.data('digest'),
                                filename: $article.data('filename'),
                                image_url: $article.data('image')
                            });
                            mc.mpnewsCount += 1;
                        });
                        break;
                    case 'sms':
                        mc.message[type] = $content.val();
                        break;
                }
            },
            allChecked: function () {
                // 判断当前发送对象列表中的所有部门/用户是否已被全部选定
                var allChecked = true;

                $('#targets-container').find('label').each(
                    function (i, target) {
                        if ($(target).find('input').prop('checked') === false) {
                            allChecked = false;
                        }
                    }
                );

                return allChecked;
            },
            timing: function () {
                var $id = $('#id'),
                    $timing = $('#timing'),
                    $time = $('#time'),
                    date = new Date(),
                    hours = date.getHours(),
                    now = wap.today(),
                    minutes = date.getMinutes();

                if (hours < 10) { hours = '0' + hours; }
                if (minutes < 10) { minutes = '0' + minutes; }
                now += " " + hours + ":" + minutes;

                $timing.on('click', function () {
                    if ($timing.val() === '0') {
                        $timing.val('1');
                        $time.parents('.weui-cell').slideDown();
                    } else {
                        $timing.val('0');
                        $time.parents('.weui-cell').slideUp();
                    }
                });
                if ($id.length === 0) {
                    $time.val(now);
                }
                $time.datetimePicker({ min: wap.today() });
            }
        };

        return {
            index: mc.index,    // 消息中心首页
            ce: mc.ce,          // 创建或编辑消息（草稿）
            show: mc.show       // 查看已发送的消息
        }
    }
})(jQuery);