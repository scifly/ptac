//# sourceURL=create.js
page.create('formEducator', 'educators');

/** 初始化教职员工创建页面功能 */
$.getMultiScripts(['js/contact.select.js'], page.siteRoot()).done(
    function () {
        var cr = $.contactRange();
        cr.create('educators');
    }
);