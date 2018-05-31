page.loadCss('css/wap_site_module/wsm.css');
page.create('formWapSiteModule', 'wap_site_modules');
$.getMultiScripts(['js/wap_site_module/wsm.js']).done(
    function () { $.wsm().init('create'); }
);