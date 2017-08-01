$(crud.create('formScoreTotal'));

getExamSubjects();

$('#exam_id').change(function () {
    getExamSubjects();
});
/*$('#na_subject_ids span').bind("click",function () {
 getNoSubjects();
 });*/

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
                //$subjectSelect.removeAttr("disabled");
                if (result.exam_subjects.length === 0) {
                    //$subjectSelect.attr("disabled", "disabled");
                    crud.inform('出现异常', '该考试暂未设置科目', crud.failure);
                } else {
                    $array = eval(result.exam_subjects);
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
