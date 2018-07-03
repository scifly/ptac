var statType = 0; // 统计类型, 0 - 学生, 1 - 班级
$('#student').hide();
$('input[name="type"]').on('ifClicked', function () {
    statType = parseInt(this.value);
    if (statType === 1) {
        $('#student').slideUp();
    } else {
        $('#student').slideDown();
    }
});
$.getMultiScripts(['js/score/score.js']).done(
    function () {$.score().stat();}
);