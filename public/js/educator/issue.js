page.loadCss('css/educator/issue.css');
$.getMultiScripts(['js/shared/card.js']).done(
    function () { $.card().init('educators', 'formEducator'); }
);