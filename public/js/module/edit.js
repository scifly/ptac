var table = 'modules';
page.loadCss('css/upload.css');
page.edit('formModule', table);
$.getMultiScripts(['js/shared/upload.js', 'js/module/module.js']).done(
    function () {
        $.upload().init('edit', table, '应用模块');
        $.module().action(table, 'edit');
    }
);