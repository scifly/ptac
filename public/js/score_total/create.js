$(crud.create('formScoreTotal'));

/**
 * 初始化多选框内容
 */
getExamSubjects();

/**
 * 考试ID若改变执行getExamSubjects方法
 */
$('#exam_id').change(function () {
    getExamSubjects();
});

/**
 * 传考试ID给后台
 * 后台返回json字符串对应该流程下 科目id:科目名称
 */
function getExamSubjects() {
    var exam_id = $('#exam_id').val();
    var $subjectSelect = $('#subject_ids');
    var $naSubjectSelect = $('#na_subject_ids');
    $subjectSelect.empty();
    $naSubjectSelect.empty();
    $.ajax({
        type: 'GET',
        url: '/ptac/public/score_totals/getExamSubjects/' + exam_id,
        success: function (result) {
            if (result.statusCode === 200) {
                if (result.exam_subjects.length === 0) {
                    crud.inform('出现异常', '该考试暂未设置科目', crud.failure);
                } else {
                    var $array = eval(result.exam_subjects);
                    $.each($array, function () {
                        $subjectSelect.append("<option value='" + this.id + "'>" + this.name + "</option>");
                        $naSubjectSelect.append("<option value='" + this.id + "'>" + this.name + "</option>");
                    });
                }
            }
            return false;
        },
        error: function (e) {
            var obj = JSON.parse(e.responseText);
            crud.inform('出现异常', obj['message'], crud.failure);
        }
    });
}

