//# sourceURL=edit.js
page.edit('formCustodian', 'custodians');

/** 初始化监护人编辑页面功能 */
$.getMultiScripts(['js/contact.js']).done(
    function () {
        $.contact().edit('custodians');
    }
);