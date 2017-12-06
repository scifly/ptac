if (typeof tree === 'undefined') {
    $.getMultiScripts(['js/tree.crud.js'], page.siteRoot())
        .done(function() { tree.rank('menus'); });
} else {tree.rank('menus');}