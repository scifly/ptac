//# sourceURL=index.js
page.index('educators', [
    {className: 'text-center', targets: [2, 3, 4, 5]}
]);

/** 初始化教职员工首页功能 */
$.getMultiScripts(['js/contact.js']).done(
    function () { $.contact().index('educators'); }
);