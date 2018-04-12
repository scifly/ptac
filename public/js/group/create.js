var scripts = [
    'js/tree.crud.js',
    'js/group/group.js'
];
$.getMultiScripts(['js/tree.crud.js']).done(function() {
    $.getMultiScripts(['js/group/group.js']).done(function () {
        $.group().create();
    });
});