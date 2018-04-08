page.create('formScore', 'scores');
var examId = $('#exam_id');
getData(examId.val());
function getData($examId) {
    $.ajax({
        type: 'GET',
        data: {'_token': $('#csrf_token').attr('content')},
        url: 'listdatas/' + $examId,
        success: function (result) {
            $('#subject_id').html(result['subjects']);
            $('#student_id').html(result['students']);
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
}
examId.change(function () {
    var $examId = $(this).val();
    getData($examId);
});