page.index('score_totals', [
    {className: 'text-center', targets: [1, 2, 5, 7, 8, 9, 10]},
    {className: 'text-right', targets: [6]}
]);
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () {
        $.dtrange().dRange('.dtrange');
        page.initSelect2();
    }
);