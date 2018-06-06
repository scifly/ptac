//# sourceURL=create.js
page.create('formStudentAttendanceSetting', 'student_attendance_settings');
page.initParsleyRules();
page.loadCss(plugins.timepicker.css);
$.getMultiScripts([plugins.timepicker.js]).done(
    function () {
        $('.timepicker').timepicker({
            showInputs: false,
            showMeridian: false,
            minuteStep: 1
        });
    }
);