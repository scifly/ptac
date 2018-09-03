//# sourceURL=index.js
page.index('educators', [
    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6, 7, 8]},
    {className: 'searching_disabled', targets: [2]},
    {searchable: false, targets: [2]},
    {orderable: false, targets: [2]}
]);

/** 初始化教职员工首页功能 */
$.getMultiScripts(['js/contact.js']).done(
    function () { $.contact().index('educators'); }
);