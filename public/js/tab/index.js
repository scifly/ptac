page.index('tabs', [
    {className: 'text-center', targets: [2, 3, 5, 6, 7]}
]);
page.initSelect2();
$.getMultiScripts(['js/dtrange.js']).done(
    function () { $.dtrange().init('.dtrange'); }
);