var table = 'columns';
page.loadCss('css/upload.css');
page.edit('formColumn', table);
$.getMultiScripts(['js/shared/upload.js']).done(
    function () {
        $.upload().init('edit', table, '微网站模块');
    }
);