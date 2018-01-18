page.index('scores');
page.initSelect2();
var $token = $('#csrf_token').attr('content');
var $import = $('#import');
var $importPupils = $('#import-pupils');
var $file = $('#confirm-import');
var $statistics = $('#statistics');
var $exam = $('#exam');
//根据成绩变动更新班级列表
$exam.on('change', function () {
    var $examId = $(this).val();
    getSquadList($examId);
});
function getSquadList($id) {
    var $data = {'_token': $token};
    $.ajax({
        type: 'GET',
        data: $data,
        url: '../scores/clalists/' + $id,
        success: function (result) {
            $('#classId').html(result.message);
        }
    });
}
//学生成绩导入
$import.on('click', function () {
    $importPupils.modal({backdrop: true});
    //初始化班级列表
    getSquadList($('#exam').val());

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
                page.inform(
                    '操作结果', result.message,
                    result.statusCode === 200 ? page.success : page.failure
                );
            },
            error: function (result) {
                console.log(result);
                page.inform("操作失败", result.message, page.failure);

            }
        });
    });
});

//学生成绩统计
$statistics.on('click', function () {
    $('#statistics-modal').modal({backdrop: true});
    $('#confirm-statistics').off('click').click(function () {
        var $examSta = $('#exam-sta').val();
        var $data = {'_token': $token};
        $.ajax({
            type: 'GET',
            data: $data,
            url: '../scores/statistics/' + $examSta,
            success: function (result) {
                page.inform(
                    '操作结果', result.message,
                    result.statusCode === 200 ? page.success : page.failure
                );
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
