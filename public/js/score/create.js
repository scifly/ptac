page.create('formScore', 'scores');

// $('#exam_id').change(function () {
//     var $examId = $(this).attr('data-values');
//     $.ajax({
//         type: 'GET',
//         data: {'_token': $('#csrf_token').attr('content')},
//         url: 'attendance_rules/' + $examId,
//         success: function (result) {
//             if (result.statusCode === 200) {
//             } else {
//                 $.alert(result.data);
//             }
//         }
//     });
// });