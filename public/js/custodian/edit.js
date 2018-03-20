//# sourceURL=edit.js
page.edit('formCustodian', 'custodians');

/** 初始化监护人编辑页面功能 */
$.getMultiScripts(['js/contact.select.js'], page.siteRoot()).done(
    function () {
        var cr = $.contactRange();
        cr.edit('custodians');
    }
);