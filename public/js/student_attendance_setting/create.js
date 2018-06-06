page.create('formStudentAttendanceSetting', 'student_attendance_settings');
page.initParsleyRules();
page.loadCss(plugins.timepicker.css);
$.getMultiScripts([plugins.timepicker.js]).done(
    function () {
        $('.timepicker').timepicker({ showInputs: false });
    }
);
// function datetime($initTime) {
//     $initTime.timepicker({
//         timeFormat: 'hh:mm:ss'
//     })
// }
//
// if (!($.fn.timepicker)) {
//     page.loadCss(plugins.timepicker.css);
//     $.getMultiScripts([plugins.timepicker.js, plugins.timepicker.jscn])
//         .done(function () {
//             datetime($(".start-time"));
//             datetime($(".end-time"));
//         });
// } else {
//     datetime($(".start-time"));
//     datetime($(".end-time"));
// }