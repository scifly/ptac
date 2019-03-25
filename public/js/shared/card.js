//# sourceURL=card.js
(function ($) {
    $.card = function (options) {
        var card = {
            options: $.extend({}, options),
            issue: function (table, formId, action) {
                var $sectionId = $('#section_id'),
                    $list = $('tbody'),
                    empty = $list.html();

                if (typeof action === 'undefined') {
                    card.onSectionChange($sectionId, empty);
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

                card.onSectionChange($sectionId, empty);
                card.onPermit(formId);
                page.initSelect2();
                page.initBackBtn(table);
            },
            onSectionChange: function ($sectionId, empty) {
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
                        url: 'issue',
                        data: {
                            _token: page.token(),
                            sectionId: $sectionId.val()
                        },
                        success: function (result) {
                            $('.overlay').hide();
                            $('tbody').html(
                                result !== '' ? result
                                    : '<tr><td class="text-center" colspan="5">- 暂无数据 -</td></tr>'
                            );
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
            onPermit: function (formId) {
                var $permit = $('#permit');

                $('#' + formId).on('submit', function () { return false; });
                $permit.on('click', function () {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: 'permit',
                        data: {
                            userIds: $('input[name=user_ids]').val(),
                            turnstileIds: $('#turnstile_ids').val(),
                            _token: page.token()
                        },
                        success: function (result) {
                            $('.overlay').hide();
                            page.inform(result['title'], result['message'], page.success);
                        },
                        error: function (e) { page.errorHandler(e); }
                    });
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

        return { issue: card.issue };
    }
})(jQuery);