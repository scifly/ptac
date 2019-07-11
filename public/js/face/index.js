page.index('faces', [
    'create,edit',
    {className: 'text-center', targets: [1, 2, 3, 4, 5]},
    {className: 'searching_disabled', targets: [4]},
    {searchable: false, targets: [4]},
    {orderable: false, targets: [4]}
]);
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () { $.dtrange().dRange('.dtrange'); }
);