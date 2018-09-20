page.initDatatable('student_attendances',[
    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6]},
]);
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