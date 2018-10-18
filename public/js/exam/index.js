page.index('exams', [
    {className: 'text-center', targets: [1, 2, 5, 6, 7, 8]},
    {className: 'text-right', targets: [3, 4]},
]);
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () {
        $.dtrange().init('.dtrange');
        $.dtrange().init('.drange');
        page.initSelect2();
    }
);
