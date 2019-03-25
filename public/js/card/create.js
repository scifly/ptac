page.loadCss('css/card/card.css');
$.getMultiScripts(['js/shared/card.js']).done(
    function () { $.card().issue('cards', 'formCard', 'create'); }
);