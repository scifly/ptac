page.loadCss('css/educator/issue.css');
$.getMultiScripts(['js/shared/cf.js']).done(
    function () { $.cf().issue('educators', 'formEducator'); }
);