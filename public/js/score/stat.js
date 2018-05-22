var statType = 0; // 统计类型, 0 - 学生, 1 - 班级
$('input[name="type"]').on('ifClicked', function () {
    statType = parseInt(this.value);
});
$.getMultiScripts(['js/score/score.js']).done(function () {
    $.score().stat();
});