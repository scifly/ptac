//# sourceURL=card.js
(function ($) {
    $.card = function (options) {
        var card = {
            options: $.extend({}, options),
            index: function (table) {
                $.map(
                    ['issue', 'permit'],
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
                    card.onSectionChange($sectionId, empty, 'issue');
                }
                card.onIssue(formId, action);
                page.initBackBtn(table);
                page.initSelect2();
                card.onInput();
            },
            permit: function (table, formId) {
                var $sectionId = $('#section_id'),
                    $list = $('tbody'),
                    empty = $list.html();

                card.onSectionChange($sectionId, empty, 'permit');
                card.onPermit(table, formId);
                page.initSelect2();
                page.initICheck();
                page.initBackBtn(table);
                $.getMultiScripts(['js/shared/dtrange.js']).done(
                    function () { $.dtrange().dRange('.drange');}
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
                            function () { $('.' + target + 's').iCheck('check'); }
                        );
                    }
                );
                $.map(
                    {contact: 'user_ids', gate: 'turnstile_ids'},
                    function (item, target) {
                        $(document).on('ifUnchecked', '.' + target, function () {
                            $('.' + target + 's').iCheck(
                            $('input[name="' + item + '[]"]:checked').length > 0 ? 'check' : 'uncheck'
                            )
                        });
                    }
                );
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
                        },
                        error: function (e) { page.errorHandler(e); }
                    });
                });
            },
            onIssue: function (formId, action) {
                var $issue = $('#issue');
                $('#' + formId).on('submit', function () { return false; });
                $(document).keypress(function (e) {
                    if (e.which === 13)  return false;
                });
                $issue.on('click', function () {
                    var data = {}, type = 'POST', url = 'issue';
                    $('input[name=sn]').each(function () {
                        var sn = $(this).val(),
                            uid = $(this).data('uid');

                        data[uid] = (typeof action === 'undefined' || action === 'create') ? sn
                            : { sn: sn, status: $($(this).parent().next().children()[0]).val() }
                    });
                    if (typeof action !== 'undefined') {
                        url = page.siteRoot() + 'cards/' + (action === 'create' ? 'store' : 'update');
                        if (action === 'edit') { type = 'PUT'; }
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
                        error: function (e) { page.errorHandler(e); }
                    });
                });
            },
            onPermit: function (table, formId) {
                $('#' + formId).on('submit', function () { return false; });
                $('#permit').on('click', function () {
                    page.ajaxRequest(
                        'POST', table + '/permit',
                        page.formData($('#' + formId))
                    );
                });
            },
            onInput: function () {
                $(document).on('keyup', 'input', function() {
                    if ($(this).val().length === parseInt($(this).attr('maxlength'))) {
                        var i = parseInt($(this).data('seq')) + 1;
                        $('input[data-seq=' + i + ']').focus();
                    }
                });
            }
        };

        return {
            index: card.index,
            issue: card.issue,
            permit: card.permit
        };
    }
})(jQuery);