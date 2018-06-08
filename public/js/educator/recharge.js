//# sourceURL=recharge.js
var $form = $('#formEducator'),
    id = $('#id').val(),
    $quote = $('#quote');

page.initBackBtn('educators');
$form.parsley().on('form:validated', function () {
    if ($('.parsley-error').length === 0) {
        $('.overlay').show();
        $.ajax({
            type: 'PUT',
            dataType: 'json',
            url: page.siteRoot() + 'educators/recharge/' + id,
            data: $form.serialize(),
            success: function (result) {
                $('.overlay').hide();
                $quote.html(result['quote'] + ' 条');
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