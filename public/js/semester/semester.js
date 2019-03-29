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
                                    { locale: page.drLocale() },
                                    function (start, end) {
                                        $daterange.find('span').html(
                                            start.format('YYYY年MM月DD日') + ' - ' +
                                            end.format('YYYY年MM月DD日')
                                        );
                                        $startDate.val(start.format('YYYY-MM-DD'));
                                        $endDate.val(end.format('YYYY-MM-DD'));
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