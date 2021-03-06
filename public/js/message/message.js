//# sourceURL=message.js
(function ($) {
    $.message = function (options) {
        var message = {
            options: $.extend({}, options),
            mpnews: { articles: [] },
            mpnewsCount: 0,
            dtOptions: [{className: 'text-center', targets: [2, 3, 4, 5, 6]}],
            id: 0,
            index: function () {
                var $batchBtns = $('.btn-group'),
                    $messageContent = $('#message-content');

                // 加载消息中心css
                page.loadCss('css/message/message.css');
                // 初始化下拉列表
                message.initSelect2();
                // 初始化消息类型卡片悬停特效、input parsley验证规则
                message.initTabs();
                // 初始化上传文件的事件
                message.initUpload();
                // 初始化移除上传文件的事件
                message.removeFile();
                // 初始化发送时间daterangepicker
                message.initTimer();
                // 初始化发送对象
                message.initTargets();
                // 初始化图文
                message.initMpnews();
                // 初始化短信
                message.initSms();
                // 初始化发送、预览、存为草稿及定时发送等功能
                message.initAction();
                // 初始化发件箱/收件箱
                message.initList();
                // 将‘消息内容’表单的所有输入框的背景置为米色
                $messageContent.find(':input').css('background-color', 'beige');
                // 隐藏'已发送'消息列表对应的批处理按钮组
                $batchBtns.hide();
            },
            initSelect2: function () {
                var $messageTypeId = $('#message_type_id');

                // 初始化应用下拉列表
                page.initSelect2([{
                    option: {
                        templateResult: page.formatStateImg,
                        templateSelection: page.formatStateImg
                    },
                    id: 'app_ids'
                }]);
                // 初始化消息类型select2控件
                $.getMultiScripts([plugins.select2.js]).done(
                    function () {
                        $.getMultiScripts([plugins.select2.jscn]).done(
                            function () { $messageTypeId.select2();}
                        );
                    }
                );
            },
            initTabs: function () {
                var $messageContent = $('#message-content');

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

                    message.initUpload();
                    message.removeValidation();
                    $messageContent.find('.tab-pane').hide();
                    message.refreshValidation(anchor);
                    $(anchor).show();
                });
                page.refreshTabs();
            },
            initUpload: function () {
                $(document).off('change', '.file-upload').on('change', '.file-upload',
                    function () {
                        if ($(this).val() !== '') { message.upload($(this)); }
                    }
                );
            },
            initTargets: function () {
                // 加载联系人树
                $.getMultiScripts(['js/shared/tree.js']).done(
                    function () { $.tree().list('messages/index', 'contact'); }
                );
            },
            initMpnews: function () {
                var $contentMpnews = $('#content_mpnews'),
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
                    $coverContainer = $('#cover-container');

                // 打开添加图文弹窗
                $addMpnews.on('click', function () {
                    if (message.mpnewsCount === 8) {
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
                    $modalMpnews.find('input, textarea').css('background-color', 'beige');
                    $modalMpnews.modal({ backdrop: true });
                });
                // 编辑图文
                $(document).on('click', '.mpnews', function () {
                    var ids = $(this).attr('id').split('-'),
                        id = ids[ids.length - 1],
                        news = message.mpnews['articles'][id],
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
                    $mediaId.after($('<img alt=""' + ' />', {'src': news['image_url'], 'style': 'height: 200px;'}).prop('outerHTML'));
                    $removeMpnews.show();
                    $modalMpnews.find('input, textarea').css('background-color', 'beige');
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
                            message.mpnews['articles'].push(article);
                            imgAttrs = {
                                'class': 'mpnews',
                                'src': imageUrl,
                                'title': title,
                                'id': 'mpnews-' + message.mpnewsCount
                            };
                            $contentMpnews.append($('<img alt=""' + ' />', imgAttrs).prop('outerHTML'));
                            message.mpnewsCount += 1;
                        } else {
                            // 更新图文
                            var $mpnews = $($contentMpnews.children('img')[id]);

                            message.mpnews['articles'][id] = article;
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
                    message.mpnews['articles'].splice(id, 1);
                    // 从图文列表中移除
                    $('#mpnews-' + id).remove();
                    // 重建图文索引
                    $contentMpnews.find('img').each(function () {
                        $(this).attr('id', '#mpnews-' + i);
                        i++;
                    });
                    message.mpnewsCount--;
                    page.inform('消息中心', '已将指定图文删除', page.success);
                });
            },
            initSms: function () {
                var smsMaxlength = $('#sms-maxlength').val(),
                    $smsLength = $('#sms-length'),
                    $contentSms = $('#content_sms'),
                    $smsContent = $('#sms-content'),
                    currentLength = '',
                    availableLength = '';

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
            },
            initTimer: function () {
                page.loadCss(plugins.daterangepicker.css);
                $.getScript(
                    page.siteRoot() + plugins.daterangepicker.moment,
                    function () {
                        $.getScript(
                            page.siteRoot() + plugins.daterangepicker.js,
                            function () {
                                var today = new Date();

                                $('#time').daterangepicker({
                                    locale: {
                                        format: "YYYY-MM-DD hh:mm",
                                        applyLabel: "确定",
                                        cancelLabel: "取消",
                                        weekLabel: "W",
                                        daysOfWeek: ["日", "一", "二", "三", "四", "五", "六"],
                                        monthNames: [
                                            "一月", "二月", "三月", "四月", "五月", "六月",
                                            "七月", "八月", "九月", "十月", "十一月", "十二月"
                                        ],
                                        firstDay: 1
                                    },
                                    minDate: today,
                                    singleDatePicker: true,
                                    drops: 'up',
                                    timePicker: true,
                                    showDropdowns: true,
                                    timePicker24Hour: true,
                                    minYear: today.getFullYear(),
                                    autoUpdateInput: true,
                                    maxYear: parseInt(moment().format('YYYY'), 10)
                                });
                            }
                        )
                    }
                );
            },
            initAction: function () {
                var $targetIds = $('#selected-node-ids'),
                    $timing = $('#timing'),
                    $send = $('#send'),
                    $preview = $('#preview'),
                    $schedule = $('input[name="schedule"]'),
                    $draft = $('#draft');

                page.initICheck();
                $send.on('click', function () {
                    $targetIds.attr('required', 'true');
                    return message.action('send');
                });
                $preview.on('click', function () {
                    $targetIds.removeAttr('required');
                    return message.action('preview');
                });
                $schedule.on('ifChecked', function () {
                    $timing.toggle('slow', function() {
                        // $draft.prop('disabled', $timing.is(':visible'));
                    });
                });
                $draft.on('click', function () {
                    return message.action('draft');
                });
            },
            initList: function () {
                var $sections = $('.action-type'),
                    $batchBtns = $('.btn-group'),
                    $tabSend = $('a[href="#tab01"]'),
                    $tabSendContent = $('#tab01'),
                    $tabSent = $('a[href="#tab02"]'),
                    $tabSentContent = $('#tab02'),
                    $tabReceived = $('a[href="#tab03"]'),
                    $textContent = $('#text-content'),
                    $videoTitle = $('#video-title'),
                    $videoDescription = $('#video-description'),
                    $messageContent = $('#message-content'),
                    $cardTitle = $('#card-title'),
                    $cardDescription = $('#card-description'),
                    $cardUrl = $('#card-url'),
                    $cardBtntxt = $('#card-btntxt'),
                    $addMpnews = $('#add-mpnews'),
                    $smsContent = $('#sms-content');

                // 重新加载datatable
                $tabSent.on('click', function () {message.loadDt('data-table');});
                $tabReceived.on('click', function () {message.loadDt('data-table-r');});
                $.getMultiScripts(['js/shared/dtrange.js']).done(
                    function () {
                        $.dtrange().dRange('.dtrange');
                        page.initSelect2();
                    }
                );
                // 显示/隐藏批处理按钮组
                $sections.on('click', function () {
                    var href = $(this).find('a').attr('href');

                    if ($.inArray(href, ['#tab02', '#tab03']) !== -1) {
                        $batchBtns.slideDown();
                        $('#batch-enable, #batch-disable').attr('data-field', href === '#tab02' ? 'sent' : 'read');
                        $('#batch-enable').attr('title', href === '#tab02' ? '批量标记已发' : '批量标记已读');
                        $('#batch-disable').attr('title', href === '#tab02' ? '批量标记未发' : '批量标记未读');
                    } else {
                        $batchBtns.slideUp();
                    }
                });
                // 编辑草稿
                $(document).on('click', '.fa-edit', function () {
                    var $id = $('#id'),
                        id = message.messageId($(this));

                    $tabSent.parent().removeClass('active');
                    $tabSentContent.removeClass('active');
                    $tabSend.parent().addClass('active');
                    $tabSendContent.addClass('active');
                    $batchBtns.hide();
                    $id.val(id);
                    $('.overlay').show();
                    $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        url: page.siteRoot() + 'messages/edit/' + id,
                        success: function (result) {
                            var $msgTypeId = $('#message_type_id'), $tabTitle,
                                html = '', type = result['message']['msgtype'],
                                tagIds = result['tagIds'], $tagId = $('#tag_id'),
                                $tagIds = $('#tag_ids'), $templateId = $('#template_id'),
                                $container, mediaId, src, $time = $('#time'), imgAttrs,
                                uploadTypes = ['image', 'audio', 'video', 'file'];

                            if (type === 'textcard') { type = 'card'; }
                            if (type === 'voice') { type = 'audio'; }

                            $container = $('#content_' + type);
                            // 设置消息类型
                            $msgTypeId.val(result['messageTypeId']).trigger('change');
                            // 选定标签(公众号：单选)
                            if ($tagId.length > 0) { $tagId.val(tagIds[0]).trigger('change'); }
                            // 选定标签(企业号：多选)
                            if ($tagIds.length > 0) {
                                for (i = 0; i < tagIds.length; i++) {
                                    $('#tag_ids option[value=' + tagIds[i] +']').attr('selected', true);
                                }
                                $tagIds.trigger('change');
                            }
                            // 选定模板
                            if ($templateId.length > 0) {
                                $templateId.val(result['templateId']).trigger('change');
                            }
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
                            message.removeValidation();
                            message.refreshValidation('#content_' + type);
                            switch (type) {
                                case 'text':
                                    $textContent.val(result['message'][type]['content']);
                                    break;
                                case 'image':
                                    imgAttrs = {
                                        'src':  src,
                                        'style': 'height: 200px;',
                                        'title': '文件名：' + message.filename(src)
                                    };
                                    html += $('<img alt=""' + ' />', imgAttrs).prop('outerHTML');
                                    break;
                                case 'audio':
                                    html += '<i class="fa fa-file-sound-o"> ' + message.filename(src) + '</i>';
                                    break;
                                case 'video':
                                    var video = result['message']['video'];
                                    $videoTitle.val(video['title']);
                                    $videoDescription.val(video['description']);
                                    html += '<video height="200" controls><source src="' + src + '" type="video/mp4"></video>';
                                    $container = $('#video-container');
                                    break;
                                case 'file':
                                    html += '<i class="fa fa-file-o"> ' + message.filename(src) + '</i>';
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
                                    message.mpnews = result['message'][type];
                                    message.mpnewsCount = message.mpnews['articles'].length;
                                    $addMpnews.siblings().remove();
                                    for (var i = 0; i < message.mpnewsCount; i++) {
                                        imgAttrs = {
                                            class: 'mpnews',
                                            src: message.mpnews['articles'][i]['image_url'],
                                            title: message.mpnews['articles'][i]['title'],
                                            id: 'mpnews-' + i
                                        };
                                        mpnewsList += $('<img alt=""' + ' />', imgAttrs).prop('outerHTML');
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
                                message.displayFile($container, mediaId, src, html);
                            }
                            $('#schedule' + (result['timing'] ? 1 : 2)).iCheck('check');
                            $time.val(result['time'] ? result['time'] : message.currentTime());
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
                        url: page.siteRoot() + 'messages/show/' + message.messageId($(this)),
                        success: function (result) {
                            var $show = $('#modal-show');
                            $show.html(result);
                            $show.modal({backdrop: true});
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
                // 打开删除对话框
                $(document).on('click', '.fa-remove', function () {
                    message.id = $(this).parents().eq(0).attr('id');
                    $('#modal-delete').modal({backdrop: true});
                });
                // 删除记录
                $('#confirm-delete').on('click', function () {
                    page.ajaxRequest(
                        'DELETE',
                        'messages/delete/' + message.id,
                        {_token: page.token()},
                        message.loadDt
                    );
                });
                // page.remove('messages', message.dtOptions);
            },
            initEditor: function () {
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
            },
            action: function (action) {
                var icon = page.info,
                    uri = 'send',
                    $targetIds = $('#selected-node-ids'),
                    $formMessage = $('#formMessage'),
                    $contentMpnews = $('#content_mpnews'),
                    requestType = 'POST',
                    formData = message.data(),
                    paths = $('#message-content').find('.tab-pane.active').attr('id').split('_'),
                    type = paths[1];

                switch (action) {
                    case 'send':
                        break;
                    case 'preview':
                        formData = message.data(true);
                        break;
                    case 'draft':
                        var $id = $('#id');
                        uri = $id.val() !== '' ? ('update/' + $id.val()) : 'store';
                        requestType = $id.val() !== '' ? 'PUT' : 'POST';
                        icon = page.success;
                        delete formData['time'];
                        break;
                    default:
                        break;
                }
                if (!$formMessage.parsley().validate()) { return false; }
                if ($targetIds.val() === '' && action !== 'preview') {
                    page.inform('消息中心', '请选择发送对象', page.failure);
                    return false;
                }
                if (
                    $.inArray(type, ['image', 'audio', 'video', 'file']) !== -1 &&
                    $('#content_' + type).find('.media_id').val() === ''
                ) {
                    var fileTypes = {
                        image: '图片',
                        audio: '语音',
                        video: '视频',
                        file: '文件'
                    };
                    page.inform('消息中心', '请上传需要发送的' + fileTypes[type], page.failure);
                    return false;
                }
                if (type === 'mpnews' && !$contentMpnews.find('img').length) {
                    page.inform('消息中心', '请添加至少1个图文内容', page.failure);
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
            },
            data: function (preview = false) {
                var $messageTypeId = $('#message_type_id'),
                    $textContent = $('#text-content'),
                    $videoTitle = $('#video-title'),
                    $videoDescription = $('#video-description'),
                    $cardTitle = $('#card-title'),
                    $cardDescription = $('#card-description'),
                    $cardUrl = $('#card-url'),
                    $cardBtntxt = $('#card-btntxt'),
                    $smsContent = $('#sms-content'),

                    targetIds = preview ? 'user-0-' + $('#userId').val() : $('#selected-node-ids').val(),
                    paths = $('#message-content').find('.tab-pane.active').attr('id').split('_'),
                    type = paths[paths.length - 1],
                    $container = $('#content_' + type),
                    messageId = $('#id').val(),
                    content = {}, formData,
                    $timer = $('#timing'),
                    $time = $('#time'),
                    mediaId, path, uploadTypes = ['image', 'audio', 'video', 'file'],
                    types = { audio: 'voice', card: 'textcard' },
                    key = $.inArray(type, ['audio', 'card']) === -1 ? type : types[type];

                formData = {
                    _token: page.token(),
                    type: key,
                    targetIds: targetIds,
                    message_type_id: $messageTypeId.val(),
                };
                if (preview) { $.extend(formData, { preview: 1 }); }
                if ($.inArray(type, uploadTypes) > -1) {
                    mediaId = $container.find('.media_id').val();
                    path = $container.find('.media_id').attr('data-path');
                }
                switch (type) {
                    case 'text':
                        $textContent.attr('required', 'true');
                        content[key] = { content: $textContent.val() };
                        break;
                    case 'image':
                    case 'audio':
                    case 'file':
                    case 'video':
                        content[key] = {
                            media_id: mediaId,
                            path: path
                        };
                        if (type === 'video') {
                            $.extend(content[key], {
                                title: $videoTitle.val(),
                                description: $videoDescription.val()
                            });
                        }
                        break;
                    case 'card':    // 卡片
                        content[key] = {
                            title: $cardTitle.val(),
                            description: $cardDescription.val(),
                            url: $cardUrl.val(),
                            btntxt: $cardBtntxt.val()
                        };
                        break;
                    case 'mpnews': // 图文
                        content[key] = {
                            articles: message.mpnews['articles']
                        };
                        break;
                    case 'sms': // 短信
                        content[key] = $smsContent.val();
                        break;
                    default:
                        break;
                }
                if ($timer.is(':visible')) {
                    $.extend(formData, { time: $time.val() });
                }
                if (messageId !== '') {
                    $.extend(formData, { id: messageId });
                }

                return $.extend(formData, content);
            },
            loadDt: function (dtId) {
                var $dtSent = $('#data-table'),
                    $dtReceived = $('#data-table-r');

                if (typeof dtId === 'undefined') {
                    dtId = $dtSent.is(':visible') ? 'data-table' : 'data-table-r';
                }
                if ($.fn.dataTable) {
                    $dtSent.dataTable().fnDestroy();
                    $dtReceived.dataTable().fnDestroy();
                }
                if (dtId === 'data-table') {
                    page.initDatatable('messages', message.dtOptions);
                } else {
                    page.initDatatable('messages', message.dtOptions, 'index', dtId, 1);
                }
            },
            messageId: function ($button) {
                return $button.parents().eq(0).attr('id').split('_')[1];
            },
            removeValidation: function () {
                $('#message-content').find(':input').removeAttr(
                    'required data-parsley-length maxlength'
                );
            },
            refreshValidation: function (anchor) {
                var $textContent = $('#text-content'),
                    $videoTitle = $('#video-title'),
                    $videoDescription = $('#video-description'),
                    $cardTitle = $('#card-title'),
                    $cardDescription = $('#card-description'),
                    $cardUrl = $('#card-url'),
                    $smsContent = $('#sms-content');

                switch (anchor) {
                    case '#content_text':
                        $textContent.attr('required', 'true');
                        break;
                    case '#content_video':
                        $videoTitle.attr({
                            'required': 'true',
                            'maxlength': 128
                        });
                        $videoDescription.attr('maxlength', 512);
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
                    case '#content_sms':
                        $smsContent.attr({
                            'required': 'true',
                            'data-parsley-length': '[2,300]'
                        });
                        break;
                    default:
                        break;
                }
            },
            displayFile: function ($container, mediaId, src, html) {
                var $uploadBtn = $container.find('.upload-button'),
                    $label = $uploadBtn.find('label'),
                    $mediaId = $uploadBtn.find('.media_id'),
                    $removeFile = $uploadBtn.find('.remove-file'),
                    $file = $mediaId.next();

                $container.find('.media_id').val(mediaId).attr('data-path', src);
                $label.html('<i class="fa fa-pencil"> 更换</i>');
                $removeFile.show();
                if ($file.attr('class') !== 'help-block') {
                    $file.remove();
                }
                $mediaId.after(html);
            },
            upload: function ($file) {
                var file = $file[0].files[0],
                    types = $file.attr('id').split('-'),
                    type = types[types.length - 1],
                    names = file.name.split('.'),
                    imgAttrs = {}, data = new FormData(),
                    ext = names[names.length - 1].toUpperCase();

                if ($.inArray(ext, ['JPG', 'PNG', 'AMR', 'MP4']) === -1 && type !== 'file') {
                    page.inform('消息中心', '不支持这种文件格式', page.failure);
                    return false;
                }
                page.inform('消息中心', '文件上传中...', page.info);
                $('.overlay').show();
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
                                html += $('<img alt=""' + ' />', imgAttrs).prop('outerHTML');
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
                                html += $('<img alt=""' + ' />', imgAttrs).prop('outerHTML');
                                $container = $('#cover-container');
                                break;
                            default:
                                return false;
                        }
                        message.displayFile($container, result['data']['media_id'], src, html);
                    },
                    error: function (e) {
                        page.errorHandler(e);
                        $('.file-upload').val('');
                    }
                });
                return false;
            },
            removeFile: function () {
                $(document).on('click', '.remove-file', function () {
                    var $messageContent = $('#message-content'),
                        types = $(this).prev().attr('id').split('-'),
                        type = types[types.length - 1],
                        $container = $messageContent.find('.tab-pane.active'),
                        $uploadBtn = $container.find('.upload-button'),
                        $label = $uploadBtn.find('label'),
                        $removeFile = $uploadBtn.find('.remove-file'),
                        $mediaId = $uploadBtn.find('.media_id'),
                        $file = $mediaId.next(),
                        labelType = {
                            image: '图片',
                            audio: '语音',
                            video: '视频',
                            file: '文件',
                            mpnews: '封面图'
                        };

                    $label.html('<i class="fa fa-cloud-upload"></i> 上传' + labelType[type]);
                    $removeFile.hide();
                    $mediaId.val('');
                    $file.remove();
                    $('#file-' + type).val('');
                });
            },
            filename: function (uri) {
                var paths = uri.split('/');
                return paths[paths.length - 1];
            },
            currentTime: function () {
                var date = new Date(),
                    year = date.getFullYear(),
                    month = date.getMonth() + 1,
                    day = date.getDate(),
                    hour = date.getHours(),
                    minute = date.getMinutes();

                if (month < 10) { month = '0' + month; }
                if (day < 10) { day = '0' + day; }
                if (hour < 10) { hour = '0' + hour; }
                if (minute < 10) { minute = '0' + minute; }

                return year + '-' + month + '-' + day + ' ' + hour + ':' + minute;
            }
        };

        return { index: message.index };
    }
})(jQuery);