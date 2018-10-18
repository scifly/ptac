$.getMultiScripts(['js/shared/atsettings.js']).done(
    function () {
        $.atsettings().init(
            'edit',
            'educator_attendance_settings',
            'formEducatorAttendanceSetting'
        )
    }
);