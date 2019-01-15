var $targetId = $('#target_id'),
    $loadMore = $('.weui-loadmore'),
    $search = $('#search'),
    $examList = $('#exams'),
    // $examLink = $('.exam-link'),
    $tips = $('.weui-loadmore__tips'),
    start = 0;

$targetId.on('change', function () { examList(false); });
$search.on('input', function () { examList(false) });
$loadMore.click(function () { start++; examList(true); });
$(document).on('click', '.exam-link', function () {
    window.location = 'sc/detail?examId=' + $(this).data('value') +
        '&targetId=' + $targetId.val() +
        ($(this).data('type') === 'student' ? '&student=1' : '');
});
function examList(more) {
    $loadMore.show();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'score_centers',
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
                // $loadMore.hide();
                if (!more) $tips.html('暂无考试');
            }
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
}
