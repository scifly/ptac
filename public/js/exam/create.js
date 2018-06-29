page.create('formExam', 'exams');

$.getScript(
    page.siteRoot() + plugins.daterangepicker.moment,
    function () {
        $.getScript(
            page.siteRoot() + plugins.daterangepicker.js,
            function () {
                page.loadCss(plugins.daterangepicker.css);
                $('#daterange').daterangepicker({locale: page.dateRangeLocale()});
            }
        )
    }
);