$.getMultiScripts(['js/atsettings.js']).done(
    function () {
        $.atsettings().init(
            'create',
            'student_attendance_settings',
            'formStudentAttendanceSetting'
        )
    }
);