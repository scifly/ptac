getExamSubjects();
if ($('#subject_select_ids').val()) {
    var $array_sub_ids = $('#subject_select_ids').val().split(",");
}
$('#exam_id').change(function () {
    getExamSubjects();
});
/*$('#na_subject_ids span').bind("click",function () {
    getNoSubjects();
});*/

function getExamSubjects() {
    var exam_id = $('#exam_id').val();
    $('#subject_ids').empty();
    $('#na_subject_ids').empty();
    var $subjectSelect = $('#subject_ids');
    var $naSubjectSelect = $('#na_subject_ids');
    $.ajax({
        type: 'GET',
        url: '/ptac/public/score_totals/getExamSubjects/' + exam_id,
        success: function (result) {
            if (result.statusCode === 200) {
                //$subjectSelect.removeAttr("disabled");
                if (result.exam_subjects.length == 0) {
                    //$subjectSelect.attr("disabled", "disabled");
                    $.gritter.add({
                        title: "注意！",
                        text: "该考试暂未设置科目",
                        image: '../img/failure.jpg'
                    });
                } else {
                    $array = eval(result.exam_subjects);
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
                        if ($.inArray($(this).val(), $array_sub_ids) === -1) {
                            $(this).attr('selected', 'selected');
                        }
                    });
                }
            }
            return false;
        },
        error: function (e) {
            var obj = JSON.parse(e.responseText);
            for (var key in obj) {
                if (obj.hasOwnProperty(key)) {
                    $.gritter.add({
                        title: "科目获取失败",
                        text: obj[key],
                        image: '../img/failure.jpg'
                    });
                }
            }
        }
    });
}

/*function getNoSubjects() {
    alert('1111');
    var tempSelect =[];
    $("#subject_ids option:selected").each(function () {
        alert(this.value);
        tempSelect.push(this.value);
    });

    $("#na_subject_ids option").each(function () {
        if ($.inArray($(this).val(), tempSelect) === -1) {
            $(this).attr('selected', 'selected');
        }
    });
}*/
