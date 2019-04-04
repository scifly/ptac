page.index('passage_rules', [
    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6, 7, 8, 9]}
]);
$('#issue').on('click', function () {
    $('.overlay').show();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'issue',
        data: { _token: page.token() },
        success: function (result) {
            $('.overlay').hide();
            page.inform(result.title, result.message, page.success);
        },
        error: function (e) { page.errorHandler(e); }
    });
});