page.index('polls', [
    { className: 'text-center', targets: [1, 2, 3, 4, 5, 6] }
]);
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () { $.dtrange().dRange('.dtrange'); }
);