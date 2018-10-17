var table = 'wap_site_modules';
page.loadCss('css/upload.css');
page.create('formWapSiteModule', table);
$.getMultiScripts(['js/upload.js']).done(
    function () {
        $.upload().init('create', table, '微网站模块');
    }
);