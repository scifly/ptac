page.index('cards', [
    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6]},
    {abc: 1234}
]);
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () { $.dtrange().init('.dtrange'); }
);