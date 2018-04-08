//# sourceURL=index.js
page.index('educators', [
    {className: 'text-center', targets: [1, 2, 3]}
]);

/** 初始化教职员工首页功能 */
$.getMultiScripts(['js/contact.select.js']).done(
    function () {
        var cr = $.contactRange();
        cr.index('educators');
    }
);