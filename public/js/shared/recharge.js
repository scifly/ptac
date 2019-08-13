//# sourceURL=recharge.js
(function ($) {
    $.recharge = function (options) {
        var recharge = {
            options: $.extend({
                file: '#file-image',
                mediaId: '#media_id',
                preview: '.preview'
            }, options),
            recharge: function (table, formId) {
                var $form = $('#' + formId),
                    id = $('#id').val(),
                    $quote = $('#quote'),
                    $charge = $('#charge');

                page.initBackBtn(table);
                page.initDatatable(
                    table, [
                        {className: 'text-center', targets: [1, 2, 3]},
                        {className: 'text-left', targets: [3]}
                    ], 'recharge/' + id
                );
                $.getMultiScripts(['js/shared/dtrange.js']).done(
                    function () {
                        $.dtrange().dRange('.dtrange');
                        // page.initSelect2();
                    }
                );
                $form.parsley().on('form:validated', function () {
                    if ($('.parsley-error').length === 0) {
                        $('.overlay').show();
                        $.ajax({
                            type: 'PUT',
                            dataType: 'json',
                            url: page.siteRoot() + table + '/recharge/' + id,
                            data: $form.serialize(),
                            success: function (result) {
                                $('.overlay').hide();
                                $quote.html(result['quote'] + ' 条');
                                $charge.val('');
                                page.inform(result.title, result.message, page.success);
                            },
                            error: function (e) {
                                page.errorHandler(e);
                            }
                        });
                    }
                }).on('form:submit', function () {
                    return false;
                });
            }
        };

        return {charge: recharge.recharge}
    }
})(jQuery);