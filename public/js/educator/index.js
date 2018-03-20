//# sourceURL=index.js
page.index('educators');

/** 初始化教职员工首页功能 */
$.getMultiScripts(['js/contact.select.js'], page.siteRoot()).done(
    function () {
        var cr = $.contactRange();
        cr.index('educators');
    }
);