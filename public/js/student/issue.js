page.loadCss('css/student/issue.css');
$.getMultiScripts(['js/shared/cf.js']).done(
    function () { $.cf().issue('students', 'formStudent'); }
);