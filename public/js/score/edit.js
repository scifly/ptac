$.getMultiScripts(['js/score/score.js']).done(function () {
    $.score().edit();
});
// page.edit('formScore', 'scores');
// var $examId = $('#exam_id');
// $examId.on('change', function () {
//     $.ajax({
//         type: 'GET',
//         dataType: 'json',
//         data: {
//             _token: $('#csrf_token').attr('content')
//         },
//         url: '../edit/' + $('#id').val() + '/' + $examId.val(),
//         success: function (result) {
//             $('#subject_id').html(result['subjects']);
//             $('#student_id').html(result['students']);
//         },
//         error: function (e) {
//             page.errorHandler(e);
//         }
//     });
// });
// changeselect();
// function changeselect() {
//     var subject = $('#subject').val();
//     var student = $('#student').val();
//     $("#subject_id").val(subject);
//     $("#student_id").val(student);
// }