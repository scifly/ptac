page.loadCss('css/card/card.css');
$.getMultiScripts(['js/shared/card.js']).done(
    function () { $.card().init('cards', 'formCard', 'create'); }
);