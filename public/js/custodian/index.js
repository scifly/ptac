//# sourceURL=index.js
page.index('custodians', [
    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6, 7, 8, 9]},
    {className: 'searching_disabled', targets: [2, 4]},
    {searchable: false, targets: [2, 4]},
    {orderable: false, targets: [2, 4]}
]);
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () { $.dtrange().dRange('.dtrange'); }
);
/** 初始化监护人首页功能 */
$.getMultiScripts(['js/shared/contact.js']).done(
    function () { $.contact().index('custodians'); }
);
/** 批量发卡 */
$('#issue').on('click', function () {
    page.getTabContent(
        $('#tab_' + page.getActiveTabId()),
        'custodians/issue'
    );
});