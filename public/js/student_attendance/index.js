page.initDatatable('student_attendances');
$.getMultiScripts(['js/dtrange.js']).done(
    function () {
        $.dtrange().init('.dtrange');
        page.initSelect2();
    }
);
var $stat = $('#stat');
$stat.on('click', function() {
    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    page.getTabContent($activeTabPane, 'student_attendances/stat');
});