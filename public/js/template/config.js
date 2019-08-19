var $appId = $('#app_id'),
    $primary = $('#primary'),
    $secondary = $('#secondary'),
    $config = $('#config');

page.initSelect2();
page.initBackBtn('templates');
$primary.add($secondary).on('change', function() { validate(); });
$config.on('click', function () {
    validate();
    if ($appId.val() === null) {
        page.inform('设置所属行业', '请选择一个公众号', page.failure);
        return false;
    }
    $.ajax({
        type: 'POST',
        data: $('#formTemplate').serialize(),
        url: 'templates/store',
        dataType: 'json',
        success: function (result) {
            page.inform(result['title'], result['message'], page.success);
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
});

function validate() {
    if ($primary.val() === $secondary.val()) {
        page.inform(
            '设置所属行业', '主/副营行业不得相同', page.failure
        );
        return false;
    }
}