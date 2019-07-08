//# sourceURL=cf.js
(function ($) {
    $.cf = function (options) {
        var cf = {
            options: $.extend({}, options),
            index: function (table) {
                $.map(
                    ['issue', 'grant', 'face'],
                    function (action) {
                        $('#' + action).off().on('click', function () {
                            page.getTabContent(
                                $('#tab_' + page.getActiveTabId()),
                                table + '/' + action
                            );
                        });
                    }
                );
            },
            issue: function (table, formId, action) {
                var $sectionId = $('#section_id'),
                    $list = $('tbody'),
                    empty = $list.html();

                if (typeof action === 'undefined') {
                    cf.onSectionChange($sectionId, empty, 'issue');
                }
                cf.onIssue(formId, action);
                page.initBackBtn(table);
                page.initSelect2();
                cf.onInput();
            },
            grant: function (table, formId) {
                var $sectionId = $('#section_id'),
                    $list = $('tbody'),
                    empty = $list.html();

                cf.onSectionChange($sectionId, empty, 'grant');
                cf.onGrant(table, formId);
                page.initSelect2();
                page.initICheck();
                page.initBackBtn(table);
                $.getMultiScripts(['js/shared/dtrange.js']).done(
                    function () {
                        $.dtrange().dRange('.drange');
                    }
                );
                $.map(
                    {check: 'ifChecked', uncheck: 'ifUnchecked'},
                    function (event, action) {
                        $.map(
                            {contacts: 'contact', gates: 'gate'},
                            function (target, i) {
                                $(document).on(event, '.' + i, function () {
                                    $('.' + target).each(function () {
                                        $(this).iCheck(action);
                                    })
                                });
                            }
                        )
                    }
                );
                $.map(
                    ['contact', 'gate'],
                    function (target) {
                        $(document).on('ifChecked', '.' + target,
                            function () {
                                $('.' + target + 's').iCheck('check');
                            }
                        );
                    }
                );
                $.map(
                    {contact: 'user_ids', gate: 'turnstile_ids'},
                    function (item, target) {
                        $(document).on('ifUnchecked', '.' + target,
                            function () {
                                $('.' + target + 's').iCheck(
                                    $('input[name="' + item + '[]"]:checked').length > 0 ? 'check' : 'uncheck'
                                )
                            }
                        );
                    }
                );
            },
            face: function (table, formId, action) {
                var $sectionId = $('#section_id'),
                    $list = $('tbody'),
                    empty = $list.html();

                if (typeof action === 'undefined') {
                    cf.onSectionChange($sectionId, empty, 'face');
                }
                cf.onUpload(action, table);
                cf.onConfig(formId, action);
                page.initBackBtn(table);
                page.initSelect2();
            },
            onSectionChange: function ($sectionId, empty, action) {
                // 选择班级
                $sectionId.on('change', function () {
                    if ($sectionId.val() === '0') {
                        $list.html(empty);
                        return false;
                    }
                    $('.overlay').show();
                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        url: action,
                        data: {
                            _token: page.token(),
                            sectionId: $sectionId.val()
                        },
                        success: function (result) {
                            $('.overlay').hide();
                            $('#section').html(
                                result !== '' ? result
                                    : '<tr><td class="text-center" colspan="5">- 暂无数据 -</td></tr>'
                            );
                            page.initICheck();
                            $('input[name=contacts]').iCheck('check');
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            onIssue: function (formId, action) {
                var $issue = $('#issue');
                $('#' + formId).on(
                    'submit', function () {
                        return false;
                    }
                );
                $(document).keypress(function (e) {
                    if (e.which === 13) return false;
                });
                $issue.on('click', function () {
                    var data = {}, type = 'POST', url = 'issue';
                    $('input[name=sn]').each(function () {
                        var sn = $(this).val(),
                            uid = $(this).data('uid');

                        data[uid] = (typeof action === 'undefined' || action === 'create') ? sn
                            : {sn: sn, status: $($(this).parent().next().children()[0]).val()}
                    });
                    if (typeof action !== 'undefined') {
                        url = page.siteRoot() + 'cards/' + (action === 'create' ? 'store' : 'update');
                        if (action === 'edit') {
                            type = 'PUT';
                        }
                    }
                    $('.overlay').show();
                    $.ajax({
                        type: type,
                        dataType: 'json',
                        url: url,
                        data: {
                            _token: page.token(),
                            sns: data
                        },
                        success: function (result) {
                            $('.overlay').hide();
                            page.inform(result['title'], result['message'], page.success);
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            onGrant: function (table, formId) {
                $('#' + formId).on('submit', function () {
                    return false;
                });
                $('#grant').on('click', function () {
                    if (
                        $('input[name=gates]:checked').length === 0 ||
                        $('input[name=contacts]:checked').length === 0
                    ) {
                        page.inform('一卡通授权', '请勾选授权对象/门禁', page.failure);
                        return false;
                    }
                    page.ajaxRequest(
                        'POST', table + '/grant',
                        page.formData($('#' + formId))
                    );
                });
            },
            onConfig: function (formId, action) {
                var $config = $('#config');
                $('#' + formId).on(
                    'submit', function () {
                        return false;
                    }
                );
                $config.on('click', function () {
                    var data = {}, type = 'POST', url = 'face';
                    $('.medias').each(function () {
                        var $this = $(this),
                            media_id = $this.val(),
                            uid = $this.attr('id').split('-')[2],
                            cameraids = $('#cameraids-' + uid).val(),
                            state = $('#state-' + uid).val();

                        data[uid] = {
                            'media_id': media_id,
                            'cameraids': cameraids,
                            'state': state
                        };
                        if (typeof action !== 'undefined') {
                            url = page.siteRoot() + 'faces/' + (action === 'create' ? 'store' : 'update');
                            if (action === 'edit') type = 'PUT';
                        }
                        $('.overlay').show();
                        $.ajax({
                            type: type,
                            dataType: 'json',
                            url: url,
                            data: {_token: page.token(), faces: data},
                            success: function (result) {
                                $('.overlay').hide();
                                page.inform(result['title'], result['message'], page.success);
                            },
                            error: function (e) {
                                page.errorHandler(e);
                            }
                        });
                    });
                });
            },
            onInput: function () {
                $(document).on('keyup', 'input', function () {
                    if ($(this).val().length === parseInt($(this).attr('maxlength'))) {
                        var i = parseInt($(this).data('seq')) + 1;
                        $('input[data-seq=' + i + ']').focus();
                    }
                });
            },
            onUpload: function (action, table) {
                $(document).on('change', '.face-upload', function () {
                    var title = '设置人脸识别',
                        $this = $(this),
                        uid = $this.attr('id')[1],
                        file = $this[0].files[0],
                        id = action === 'edit' ? '/' + $('#id').val() : '',
                        data = new FormData();

                    data.append('file', file);
                    data.append('_token', page.token());
                    page.inform(title, '图片上传中...', page.info);
                    $('.overlay').show();
                    $.ajax({
                        type: 'POST',
                        url: page.siteRoot() + table + '/' + action + id,
                        data: data,
                        contentType: false,
                        processData: false,
                        success: function (result) {
                            var $preview = $('.preview-' + uid),
                                imgAttrs = {
                                    'src': '../../' + result['path'],
                                    'title': '文件名：' + result['filename']
                                };

                            $('#media-id-' + uid).val(result['id']);
                            $preview.find('img').remove();
                            $preview.append($('<img' + ' />', imgAttrs).prop('outerHTML'));
                            $('.overlay').hide();
                            page.inform(title, '图片上传成功', page.success)
                        }
                    });
                });
            }
        };

        return {
            index: cf.index,
            issue: cf.issue,
            grant: cf.grant,
            face: cf.face
        };
    }
})(jQuery);