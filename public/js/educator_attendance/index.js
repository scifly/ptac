page.initDatatable('educator_attendances', [
    {className: 'text-center', targets: [2, 3, 4]}
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
    page.getTabContent($activeTabPane, 'educator_attendances/stat');
});