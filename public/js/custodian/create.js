//# sourceURL=create.js
page.create('formCustodian', 'custodians');

/** 初始化监护人创建页面功能 */
$.getMultiScripts(['js/contact.js']).done(
    function () {
        $.contact().create('custodians');
    }
);