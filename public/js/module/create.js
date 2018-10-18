var table = 'modules';
page.loadCss('css/upload.css');
page.create('formModule', table);
$.getMultiScripts(['js/shared/upload.js']).done(
    function () {
        $.upload().init('create', table, '应用模块');
    }
);