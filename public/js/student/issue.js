page.loadCss('css/student/issue.css');
$.getMultiScripts(['js/shared/card.js']).done(
    function () { $.card().issue('students', 'formStudent'); }
);