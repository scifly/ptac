//# sourceURL=index.js
page.index('educators', [
    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6, 7]},
    {className: 'searching_disabled', targets: [2]},
    {searchable: false, targets: [2]},
    {orderable: false, targets: [2]}
]);
$.getMultiScripts([
    'js/shared/dtrange.js',
    'js/shared/contact.js',
    'js/shared/cf.js'
]).done(
    function () {
        $.dtrange().dRange('.dtrange');
        $.contact().index('educators');
        $.cf().index('educators');
    }
);