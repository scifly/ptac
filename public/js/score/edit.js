page.edit('formScore', 'scores');
var examId = $('#exam_id');
getData(examId.val());
function getData($examId) {
    $.ajax({
        type: 'GET',
        data: {'_token': $('#csrf_token').attr('content')},
        url: '../get_datas/' + $examId,
        success: function (result) {
            if (result.statusCode === 200) {
                $('#subject_id').html(result.subjects);
                $('#student_id').html(result.students);
            }
        },
        error: function () {
            page.inform('提示', '请检查考试设置！', page.failure);
        }
    });
}
examId.change(function () {
    var $examId = $(this).val();
    getData($examId);
});