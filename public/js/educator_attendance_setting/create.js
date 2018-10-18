$.getMultiScripts(['js/shared/atsettings.js']).done(
    function () {
        $.atsettings().init(
            'create',
            'educator_attendance_settings',
            'formEducatorAttendanceSetting'
        )
    }
);