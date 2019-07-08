//# sourceURL=index.js
page.index('custodians', [
    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6, 7, 8, 9]},
    {className: 'searching_disabled', targets: [2, 4]},
    {searchable: false, targets: [2, 4]},
    {orderable: false, targets: [2, 4]}
]);
$.getMultiScripts([
    'js/shared/dtrange.js',
    'js/shared/contact.js',
    'js/shared/cf.js'
]).done(
    function () {
        $.dtrange().dRange('.dtrange');
        $.contact().index('custodians');
        $.cf().index('custodians');
    }
);