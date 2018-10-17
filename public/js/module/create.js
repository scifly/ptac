var table = 'modules';
page.loadCss('css/upload.css');
page.create('formModule', table);
$.getMultiScripts(['js/upload.js']).done(
    function () {
        $.upload().init('create', table, '应用模块');
    }
);