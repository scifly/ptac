//# sourceURL=index.js
page.index('custodians');

/** 初始化监护人首页功能 */
$.getMultiScripts(['js/contact.select.js']).done(
    function () {
        var cr = $.contactRange();
        cr.index('custodians');
    }
);