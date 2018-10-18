page.create('formExam', 'exams');
$.getMultiScripts(['js/shared/dtrange.js']).done(
    function () { $.dtrange().init('.drange'); }
);