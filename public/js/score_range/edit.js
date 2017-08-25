$(crud.edit('formScoreRange','score_ranges'));

getSubjectBySchoolId();

//edit页面获取已选中的科目id
var $subjectSelectedIds = $('#subject_select_ids');
if($subjectSelectedIds.val()){
    var $array_sub_ids = $subjectSelectedIds.val().split(",");
}

//school下拉菜单改变选择调用程序
$('#school_id').change(function(){
    getSubjectBySchoolId();
});

/*
 根据选择的school_id获取该校的subjects
 */
function getSubjectBySchoolId() {
    var school_id = $('#school_id').val();
    var $subjectSelect = $('#subject_ids');
    var subjectsArr;
    $subjectSelect.empty();
    $.ajax({
        type: 'GET',
        url: '/ptac/public/subject/query/' + school_id,
        success: function(result) {
            if (result.statusCode === 200) {
                $subjectSelect.removeAttr("disabled");
                if(result.subjects.length === 0){
                    $subjectSelect.attr("disabled","disabled");
                    crud.inform('出现异常', '该校暂未设置科目', crud.failure);
                }else{
                    subjectsArr = eval(result.subjects);
                    //遍历科目数组，添加为下拉菜单的option
                    $.each(subjectsArr, function () {
                        $subjectSelect.append("<option value='"+this.id+"'>"+this.name+"</option>");
                    });
                    //选择已选中的科目
                    $subjectSelect.find("option").each(function() {
                        var $this = $(this);
                        if($.inArray($this.val(), $array_sub_ids) !== -1){
                            $this.attr('selected', 'selected');
                        }
                    });
                }
            }
            return false;
        },
        error: function(e) {
            var obj = JSON.parse(e.responseText);
            crud.inform('出现异常', obj['message'], crud.failure);
        }
    });
}