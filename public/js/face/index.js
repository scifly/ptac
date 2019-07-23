page.index('faces', [
    'create',
    {className: 'text-center', targets: [1, 2, 3, 4, 5]},
    {className: 'searching_disabled', targets: [1]},
    {searchable: false, targets: [1]},
    {orderable: false, targets: [1]}
]);
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () { $.dtrange().dRange('.dtrange'); }
);
$(document).off('click', '.fa-pencil');
$(document).on('click', '.fa-pencil', function () {
    page.getTabContent(
        $('#tab_' + page.getActiveTabId()), 'faces/create',
        [$(this).parents().eq(0).attr('id').split('_')[1]]
    );
});