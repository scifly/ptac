page.edit('formFlowType', 'flow_types');
$.getMultiScripts(['js/flow_type/ft.js']).done(
    function () { $.ft().init('edit'); }
);