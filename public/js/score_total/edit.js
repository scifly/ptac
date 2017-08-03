$(crud.edit('formScoreTotal'));

/**
 * 初始化多选框内容
 */
getExamSubjects();

/**
 *考试ID若改变执行getExamSubjects方法
 */
if ($('#subject_select_ids').val()) {
    var $array_sub_ids = $('#subject_select_ids').val().split(",");
    var $array_na_sub_ids = $('#na_subject_select_ids').val().split(",");
}
$('#exam_id').change(function () {
    getExamSubjects();
});

/**
 * 传考试ID给后台
 * 后台返回json字符串对应该流程下 科目id:科目名称
 * 判断返回值id是否存在与被选中的数组ID中，存在显示选中状态。
 */
function getExamSubjects() {
    var exam_id = $('#exam_id').val();
    var $subjectSelect = $('#subject_ids');
    var $naSubjectSelect = $('#na_subject_ids');
    $subjectSelect.empty();
    $naSubjectSelect.empty();
    $naSubjectSelect.attr("disabled", false);
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
                    $("#subject_ids option").each(function () {
                        if ($.inArray($(this).val(), $array_sub_ids) !== -1) {
                            $(this).attr('selected', 'selected');
                        }
                    });
                    $("#na_subject_ids option").each(function () {
                        if ($.inArray($(this).val(), $array_na_sub_ids) !== -1) {
                            $(this).attr('selected', 'selected');
                        }
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


