var table = 'columns';
page.loadCss('css/upload.css');
page.create('formColumn', table);
$.getMultiScripts(['js/shared/upload.js']).done(
    function () {
        $.upload().init('create', table, '微网站栏目');
    }
);