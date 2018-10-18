var table = 'modules';
page.loadCss('css/upload.css');
page.edit('formModule', table);
$.getMultiScripts(['js/shared/upload.js']).done(
    function () {
        $.upload().init('edit', table, '应用模块');
    }
);