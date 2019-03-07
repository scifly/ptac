(function ($) {
    $.card = function (options) {
        var card = {
            options: $.extend({}, options),
            init: function (table, formId) {
                var $sectionId = $('#section_id'),
                    $list = $('tbody'),
                    empty = $list.html();

                card.onClassChange($sectionId, empty);
                card.onSave(formId);
                page.initBackBtn(table);
                page.initSelect2();
                card.onInput();
            },
            onClassChange: function ($sectionId, empty) {
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
                            sedctionId: $sectionId.val()
                        },
                        success: function (result) {
                            $('.overlay').hide();
                            $list.html(
                                result !== '' ? result
                                    : '<tr><td class="text-center" colspan="4">- 暂无数据 -</td></tr>'
                            );
                        },
                        error: function (e) { page.errorHandler(e); }
                    });
                });
            },
            onSave: function (formId) {
                $('#' + formId).parsley().on('form:validated', function () {
                    var data = {};
                    $('input[name=sn]').each(function () {
                        data[$(this).data('uid')] = $(this).val();
                    });
                    $('.overlay').show();
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: 'issue',
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
                }).on('form:submit', function () {
                    return false;
                });
            },
            onInput: function () {
                $('input').on('keyup', function() {
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