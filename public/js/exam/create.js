page.create('formExam', 'exams');

$.getMultiScripts(['js/exam/exam.js']).done(
    function () { $.exam().initDateRange(); }
);