page.index('passage_logs', [
    'export',
    { className: 'text-center', targets: [1, 2, 3, 4, 6, 7]}
]);
page.initSelect2();
// 采集门禁通行记录
$('#store').on('click', function () {
    $('.overlay').show();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'store',
        data: { _token: page.token() },
        success: function (result) {
            $('.overlay').hide();
            page.inform(result['title'], result['message'], page.success);
        },
        error: function (e) { page.errorHandler(e); }
    });
});