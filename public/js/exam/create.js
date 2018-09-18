page.create('formExam', 'exams');
$.getMultiScripts(['js/dtrange.js']).done(
    function () { $.dtrange().init(); }
);