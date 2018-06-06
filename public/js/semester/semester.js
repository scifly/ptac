(function ($) {
    $.semester = function (options) {
        var semester = {
            options: $.extend({}, options),
            init: function (action) {
                var $daterange = $('#daterange'),
                    $startDate = $('#start_date'),
                    $endDate = $('#end_date');

                if (action === 'create') {
                    page.create('formSemester', 'semesters');
                } else {
                    page.edit('formSemester', 'semesters');
                }
                page.loadCss(plugins.daterangepicker.css);
                $.getScript(
                    page.siteRoot() + plugins.daterangepicker.moment,
                    function () {
                        $.getScript(
                            page.siteRoot() + plugins.daterangepicker.js,
                            function () {
                                $daterange.daterangepicker(
                                    { locale: page.dateRangeLocale() },
                                    function (start, end) {
                                        var $start = start.format('YYYY-MM-DD'),
                                            $end = end.format('YYYY-MM-DD');

                                        $daterange.find('span').html($start + ' - ' + $end);
                                        $startDate.val($start);
                                        $endDate.val($end);
                                    }
                                );
                            }
                        )
                    }
                );
            }
        };

        return { init: semester.init };
    }
})(jQuery);