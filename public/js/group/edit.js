var scripts = ['js/shared/tree.js', 'js/group/group.js'];
$.getMultiScripts(scripts).done(function() {
    page.loadCss(plugins.jstree.css);
    $.getMultiScripts([plugins.jstree.js]).done(function () {
        $.group().init();
    });
});