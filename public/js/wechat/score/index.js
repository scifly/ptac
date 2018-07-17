var $targetId = $('#target_id'),
    $loadMore = $('.loadmore'),
    $search = $('#search'),
    $examList = $('.weui-cells'),
    $examLink = $('.exam-link'),
    start = 0;

FastClick.attach(document.body);
$targetId.on('change', function () { examList(false); });
$search.on('input', function () { examList(false) });
$loadMore.click(function () { start++; examList(true); });
$examLink.on('click', function () {
    window.location = 'sc/detail?examId=' + $(this).data('value') + '&targetId=' + $targetId.val();
});
function examList(more) {
    $loadMore.show();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'sc',
        data: {
            start: start,
            keyword: $search.val(),
            target_id: $targetId.val(),
            _token: wap.token()
        },
        success: function (result) {
            var html = '', exams = result['exams'];
            if (exams.length !== 0) {
                for (var i = 0; i < exams.length; i++) {
                    html +=
                        '<a class="weui-cell weui-cell_access" href="#">' +
                            '<div class="weui-cell__bd"><p>' + exams[i]['name'] + '</p></div>' +
                            '<div class="weui-cell__ft time">' + exams[i]['start_date'] + '</div>' +
                        '</a>';
                }
                more ? $examList.append(html) : $examList.html(html);
            } else {
                $loadMore.hide();
                if (!more) { $examList.html('暂无数据'); }
            }
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
}
