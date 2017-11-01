page.edit('formProcedureStep', 'procedure_steps');

/**
 *初始化多选框内容
 */
getSchoolEducators();

/**
 * 流程ID若改变执行getSchoolEducators方法
 */
$('#procedure_id').change(function () {
    getSchoolEducators();
});
if ($('#approver_user_ids').val()) {
    var $array_appro_ids = $('#approver_select_ids').val().split(",");
}
if ($('#related_select_ids').val()) {
    var $array_relate_ids = $('#related_select_ids').val().split(",");
}

/**
 * 传流程ID给后台
 * 后台返回json字符串对应该流程下 教职工id:教职员工姓名
 * 判断返回值id是否存在与被选中的数组ID中，存在显示选中状态。
 */
function getSchoolEducators() {
    var procedure_id = $('#procedure_id').val();
    var $approver_user_ids = $('#approver_user_ids');
    var $related_user_ids = $('#related_user_ids');
    $approver_user_ids.empty();
    $related_user_ids.empty();
    $.ajax({
        type: 'GET',
        url: '/ptac/public/procedure_steps/getSchoolEducators/' + procedure_id,
        success: function (result) {
            if (result.statusCode === 200) {
                if (result.educators.length === 0) {
                    crud.inform('出现异常', '该学校暂未添加教职员工', crud.failure);
                } else {
                    var $obj = eval(result.educators);
                    for (var key in $obj) {
                        $approver_user_ids.append("<option value='" + key + "'>" + $obj[key] + "</option>");
                        $related_user_ids.append("<option value='" + key + "'>" + $obj[key] + "</option>");
                    }
                    $("#approver_user_ids option").each(function () {
                        if ($.inArray($(this).val(), $array_appro_ids) !== -1) {
                            $(this).attr('selected', 'selected');
                        }

                    });
                    $("#related_user_ids option").each(function () {
                        if ($.inArray($(this).val(), $array_relate_ids) !== -1) {
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