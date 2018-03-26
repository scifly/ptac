//# sourceURL=edit.js
page.edit('formStudent', 'students');

/** 初始化学籍编辑页面功能 */
$.getMultiScripts(['js/contact.select.js'], page.siteRoot()).done(
    function () {
        var cr = $.contactRange();
        cr.edit('students');
    }
);