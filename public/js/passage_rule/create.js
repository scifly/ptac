page.create('formPassageRule', 'passage_rules');
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () {
        $.dtrange().tRange();
        $.dtrange().dRange('.drange');
    }
);