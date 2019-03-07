page.loadCss('css/custodian/issue.css');
$.getMultiScripts(['js/shared/card.js']).done(
    function () { $.card().init('custodians', 'formCustodian'); }
);