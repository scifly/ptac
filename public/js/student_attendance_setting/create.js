$.getMultiScripts(['js/shared/atsettings.js']).done(
    function () {
        $.atsettings().init(
            'create',
            'student_attendance_settings',
            'formStudentAttendanceSetting'
        )
    }
);