page.create('formStudentAttendanceSetting', 'student_attendance_settings');
page.initParsleyRules();

function datetime($initTime) {
    $initTime.timepicker({
        timeFormat: 'hh:mm:ss'
    })
}

if (!($.fn.timepicker)) {
    page.loadCss(page.plugins.timepicker.css);
    $.getMultiScripts([page.plugins.timepicker.js, page.plugins.timepicker.jscn], page.siteRoot())
        .done(function () {
            datetime($(".start-time"));
            datetime($(".end-time"));
        });
} else {
    datetime($(".start-time"));
    datetime($(".end-time"));
}