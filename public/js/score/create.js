$.getMultiScripts(['js/score/score.js']).done(function () {
    $.score().create();
});
// page.create('formScore', 'scores');
// var $examId = $('#exam_id');
// $examId.on('change', function () {
//     $.ajax({
//         type: 'GET',
//         dataType: 'json',
//         data: {
//             _token: $('#csrf_token').attr('content')
//         },
//         url: 'create/' + $examId.val(),
//         success: function (result) {
//             var $studentId = $('#student_id'),
//                 $subjectId = $('subject_id'),
//                 $studentNext = $studentId.next(),
//                 $studentPrev = $studentId.prev(),
//                 $subjectNext = $subjectId.next(),
//                 $subjectPrev = $subjectId.prev();
//
//             $studentNext.remove();
//             $studentId.remove();
//             $studentPrev.after(result['students']);
//             $subjectNext.remove();
//             $subjectId.remove();
//             $subjectPrev.after(result['subjects']);
//
//             page.initSelect2();
//         },
//         error: function (e) {
//             page.errorHandler(e);
//         }
//     });
// });