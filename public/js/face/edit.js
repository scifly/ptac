page.loadCss('css/upload.css');
$.getMultiScripts(['js/shared/cf.js']).done(
    function () { $.cf().face('faces', 'formFace', 'edit'); }
);