page.index('scores');
page.initSelect2();
var $token = $('#csrf_token').attr('content');
var $import = $('#import');
var $importPupils = $('#import-pupils');
var $file = $('#confirm-import');
$import.on('click', function () {
    $importPupils.modal({backdrop: true});
    $file.off('click').click(function () {
        var $exam = $('#exam').val();
        var $grade = $('#gradeId').val();
        var $squad = $('#classId').val();
        var formData = new FormData();
        formData.append('file', $('#fileupload')[0].files[0]);
        formData.append('_token', $token);
        formData.append('exam_id', $exam);
        formData.append('class_id', $squad);
        formData.append('grade_id', $grade);
        $.ajax({
            url: "../scores/import",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (result) {
                if (result.error !== 0) {
                    console.log(result);
                    // page.inform("操作失败", result.message, page.failure);
                }
            },
            error: function (result) {
                console.log(result);
                // page.inform("操作失败", result.message, page.failure);

            }
        });
    });
});

// $examsName.on('change',function(){
// 	var id = $(this).val();
// 	var formData = new FormData();
//     formData.append('_token', $token.attr('content'));
//     formData.append('exam', id);
//     $.ajax({
//         url: page.siteRoot() + "scores/send",
//         type: 'POST',
//         cache: false,
//         data: formData,
//         processData: false,
//         contentType: false,
//         success: function (result) {
//             var html = '';
//             console.log(result);
//
//         }
//     });
// });
