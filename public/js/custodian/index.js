//# sourceURL=index.js
page.index('custodians');

/** 初始化监护人首页功能 */
$.getMultiScripts(['js/contact.js']).done(
    function () {
        $.contact().index('custodians');
    }
);