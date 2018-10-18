$.getMultiScripts(['js/shared/atsettings.js']).done(
    function () {
        $.atsettings().init(
            'edit',
            'student_attendance_settings',
            'formStudentAttendanceSetting'
        )
    }
);