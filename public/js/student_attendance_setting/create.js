page.create(
    'formStudentAttendanceSetting',
    'student_attendance_settings'
);
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () { $.dtrange().tRange(); }
);