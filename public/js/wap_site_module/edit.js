var table = 'wap_site_modules';
page.loadCss('css/upload.css');
page.edit('formWapSiteModule', table);
$.getMultiScripts(['js/shared/upload.js']).done(
    function () {
        $.upload().init('edit', table, '微网站模块');
    }
);