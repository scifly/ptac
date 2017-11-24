if (typeof group !== 'function') {
    var scripts = ['js/tree.crud.js', 'js/group/group.js'];
    $.getMultiScripts(scripts, page.siteRoot())
        .done(function() { group('create'); });
} else { group('create'); }