page.create('formFlowType', 'flow_types');
$.getMultiScripts(['js/flow_type/ft.js']).done(
    function () { $.ft().init('create'); }
);
