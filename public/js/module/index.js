var options = [
    {className: 'text-center', targets: [5, 6, 7, 8]},
];
page.index('modules', options);
page.initSelect2();
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () { $.dtrange().init('.dtrange'); }
);