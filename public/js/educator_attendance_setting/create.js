$.getMultiScripts(['js/atsettings.js']).done(
    function () {
        $.atsettings().init(
            'create',
            'educator_attendance_settings',
            'formEducatorAttendanceSetting'
        )
    }
);