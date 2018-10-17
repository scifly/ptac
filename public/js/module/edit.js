var table = 'modules';
page.loadCss('css/upload.css');
page.edit('formModule', table);
$.getMultiScripts(['js/upload.js']).done(
    function () {
        $.upload().init('edit', table, '应用模块');
    }
);