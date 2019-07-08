page.loadCss('css/card/card.css');
$.getMultiScripts(['js/shared/cf.js']).done(
    function () { $.cf().issue('cards', 'formCard', 'edit'); }
);