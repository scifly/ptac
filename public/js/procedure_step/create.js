page.create('formProcedureStep', 'procedure_steps');

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

/**
 * 传流程ID给后台
 * 后台返回json字符串对应该流程下 教职工id:教职员工姓名
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
                    page.inform(
                        '加载失败', '该学校暂未添加教职员工', page.failure
                    );
                } else {
                    var $obj = eval(result.educators);
                    for (var key in $obj) {
                        $approver_user_ids.append("<option value='" + key + "'>" + $obj[key] + "</option>");
                        $related_user_ids.append("<option value='" + key + "'>" + $obj[key] + "</option>");
                    }
                }
            }
            return false;
        },
        error: function(e) {
            var obj = JSON.parse(e.responseText);
            page.inform('出现异常', obj['message'], page.failure);
        }
    });
}