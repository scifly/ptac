//# sourceURL=index.js
page.index('custodians', [
    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]}
]);

/** 初始化监护人首页功能 */
$.getMultiScripts(['js/contact.js']).done(
    function () { $.contact().index('custodians'); }
);