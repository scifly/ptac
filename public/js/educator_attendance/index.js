page.initDatatable('educator_attendances');

var $stat = $('#stat');
$stat.on('click', function() {
    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    page.getTabContent($activeTabPane, 'educator_attendances/stat');
});