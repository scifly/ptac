page.index('poll_topics', [
    { className: 'text-center', targets: [1, 2, 3, 4, 5] }
]);
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () { $.dtrange().dRange('.dtrange'); }
);