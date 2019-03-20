page.edit(
    'formEducatorAttendanceSetting',
    'educator_attendance_settings'
);
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () { $.dtrange().tRange(); }
);