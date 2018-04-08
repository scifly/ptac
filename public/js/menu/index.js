if (typeof tree.index === 'undefined') {
    $.getMultiScripts(['js/tree.crud.js'])
        .done(function() { tree.index('menus'); });
} else { tree.index('menus'); }