$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () {
        page.initICheck();
        page.initSelect2();
        $.dtrange().tRange();
        $.dtrange().dRange('.drange');
    }
);