//# sourceURL=card.js
(function ($) {
    $.card = function (options) {
        var card = {
            options: $.extend({}, options),
            init: function (table, formId, action) {
                var $sectionId = $('#section_id'),
                    $list = $('tbody'),
                    empty = $list.html();

                if (typeof action === 'undefined') {
                    card.onSectionChange($sectionId, empty);
                }
                card.onSave(formId, action);
                page.initBackBtn(table);
                page.initSelect2();
                card.onInput();
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
            onSave: function (formId, action) {
                var $issue = $('#issue');
                $('#' + formId).on('submit', function () { return false; });
                $(document).keypress(function (e) {
                    if (e.which === 13)  return false;
                });
                $issue.on('click', function () {
                    var data = {}, type = 'POST', url = 'issue';
                    $('input[name=sn]').each(function () {
                        var sn = $(this).val();
                        if (typeof action === 'undefined' || action === 'create') {
                            data[$(this).data('uid')] = sn;
                        } else {
                            var next = $(this).parent().next().html(),
                                status = $($(next)[0]).val();
                            data[$(this).data('uid')] = {sn: sn, status: status}
                        }
                    });
                    if (typeof action !== 'undefined') {
                        url = action === 'create' ? 'store' : 'update';
                        if (action === 'edit') { type = 'PUT'; }
                    }
                    $('.overlay').show();
                    $.ajax({
                        type: type,
                        dataType: 'json',
                        url: '../' + url,
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
            onInput: function () {
                $(document).on('keyup', 'input', function() {
                    if ($(this).val().length === parseInt($(this).attr('maxlength'))) {
                        var i = parseInt($(this).data('seq')) + 1;
                        $('input[data-seq=' + i + ']').focus();
                    }
                });
            }
        };

        return { init: card.init };
    }
})(jQuery);