page.loadCss('css/upload.css');
$.getMultiScripts(['js/shared/cf.js']).done(
    function () { $.cf().issue('students', 'formStudent'); }
);