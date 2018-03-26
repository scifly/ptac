//# sourceURL=create.js
page.create('formStudent', 'students');

/** 初始化学籍创建页面功能 */
$.getMultiScripts(['js/contact.select.js'], page.siteRoot()).done(
    function () {
        var cr = $.contactRange();
        cr.create('students');
    }
);