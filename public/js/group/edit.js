if (typeof group !== 'function') {
    var scripts = ['js/tree.crud.js', 'js/group/group.js'];
    $.getMultiScripts(scripts)
        .done(function() { group('edit'); });
} else { group('edit'); }