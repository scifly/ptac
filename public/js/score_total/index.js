page.index('score_totals', [
    {className: 'text-center', targets: [1, 2, 3, 5, 6, 7, 8]},
    {className: 'text-right', targets: [4]}
]);
$.getMultiScripts(['js/dtrange.js']).done(
    function () { $.dtrange().init('.dtrange'); }
);