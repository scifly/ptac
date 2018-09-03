//# sourceURL=index.js
page.index('custodians', [
    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]},
    {className: 'searching_disabled', targets: [2]},
    {searchable: false, targets: [2]},
    {orderable: false, targets: [2]}
]);

/** 初始化监护人首页功能 */
$.getMultiScripts(['js/contact.js']).done(
    function () { $.contact().index('custodians'); }
);