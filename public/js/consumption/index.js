page.index('consumptions', [
    { className: 'text-center', targets: [1, 2, 3, 4]},
    { className: 'text-right', targets: [5, 6]}
]);
$('#stat').on('click', function() {
    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    page.getTabContent($activeTabPane, 'consumptions/show');
});
$('#export').on('click', function () {
    var $dt = $('data-table').DataTable();
    if (!$dt.data().count()) {
        page.inform('学生消费记录', '没有数据可导出', page.failure);
    } else {
        window.location = page.siteRoot() + 'consumptions/export';
    }
});
