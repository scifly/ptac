//# sourceURL=index.js
page.index('students', [
    {className: 'text-center', targets: [1, 2, 3, 5, 6, 7, 8, 9, 10]},
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
        $.dtrange().dRange('.drange');
        $.contact().index('students');
        $.cf().index('students');
    }
);