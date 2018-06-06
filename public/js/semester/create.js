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
                $daterange.daterangepicker({
                    locale: {
                        format: "YYYY-MM-D",
                        separator: " 至 ",
                        applyLabel: "确定",
                        cancelLabel: "取消",
                        fromLabel: "从",
                        toLabel: "到",
                        todayRangeLabel: '今天',
                        customRangeLabel: "自定义",
                        weekLabel: "W",
                        daysOfWeek: ["日", "一", "二", "三", "四", "五", "六"],
                        monthNames: [
                            "一月", "二月", "三月", "四月", "五月", "六月",
                            "七月", "八月", "九月", "十月", "十一月", "十二月"
                        ],
                        firstDay: 1
                    },
                },
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
});