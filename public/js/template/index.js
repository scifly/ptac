page.index('templates', [
    {className: 'text-center', targets: [1, 3, 4]},
    {className: 'searching_disabled', targets: [3, 4, 5]},
    {searchable: false, targets: [3, 4, 5]},
    {orderable: false, targets: [3, 4, 5]}
]);
$('#config').off().on('click', function () {
    page.getTabContent(
        $('#tab_' + page.getActiveTabId()),
        'templates/config'
    );
});