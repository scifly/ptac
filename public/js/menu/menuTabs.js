if (typeof tree === 'undefined') {
    $.getMultiScripts(['js/tree.crud.js'])
        .done(function() { tree.rank('menus'); });
} else {tree.rank('menus');}