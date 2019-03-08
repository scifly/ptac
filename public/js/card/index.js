page.index('cards', [
    'create,edit',
    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6]},
    {className: 'searching_disabled', targets: [3]},
    {searchable: false, targets: [3]},
    {orderable: false, targets: [3]}
]);
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () { $.dtrange().init('.dtrange'); }
);