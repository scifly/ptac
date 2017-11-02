if (typeof tree.index === 'undefined') {
    $.getMultiScripts(['js/tree.crud.js'], page.siteRoot())
        .done(function() { tree.index('departments'); });
} else { tree.index('departments'); }