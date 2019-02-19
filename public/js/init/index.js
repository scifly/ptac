var $all = $('#all'),
    $init = $('#init'),
    $paramId = $('#param_id'),
    $list = $('table tbody');

$paramId.on('change', function () {
    $('.overlay').show();
    $.ajax({
        type: 'POST',
        dataType: 'html',
        url: 'index',
        data: {
            _token: page.token(),
            action: 'list',
            paramId: $paramId.val()
        },
        success: function (result) {
            $('.overlay').hide();
            $list.html(result);
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
});
$all.on('click', function () { init(); });
$init.on('click', function () { init($paramId.val()); });
page.initSelect2();

function init(paramId) {
    var data = {
        _token: page.token(),
        action: 'init'
    };
    if (typeof paramId !== 'undefined') {
        $.extend(data, {paramId: paramId})
    }
    $('.overlay').show();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'index',
        data: data,
        success: function (result) {
            $('.overlay').hide();
            page.inform(result.title, result.message, page.success);
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
}