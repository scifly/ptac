//#sourceURL=index.js
page.index('cameras', [
    { className: 'text-center', targets: [1, 2, 3, 5, 6, 7]}
]);
$('#store').on('click', function () {
    $('.overlay').show();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'store',
        data: { _token: page.token() },
        success: function (result) {
            $('.overlay').hide();
            page.inform(result.title, result.message, page.success);
        },
        error: function (e) { page.errorHandler(e); }
    });
});