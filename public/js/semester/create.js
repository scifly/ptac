var $daterange = $('#daterange'),
    $startDate = $('#startDate'),
    $endDate = $('#endDate');

page.create('formSemester', 'semesters');
$.getScript(
    page.siteRoot() + plugins.daterangepicker.moment,
    function () {
        $.getScript(
            page.siteRoot() + plugins.daterangepicker.js,
            function () {
                $daterange.daterangepicker(
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