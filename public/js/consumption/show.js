page.initForm(
    'consumptions',
    'formConsumption',
    'consumptions/stat',
    'POST'
);

var $rangeId = $('#range_id');
var $students = $('#students');
var $classes = $('#classes');
var $grades = $('#grades');
$rangeId.on('change', function () {
    console.log($rangeId.val());
    switch (parseInt($rangeId.val())) {
        case 1:
            $students.show();
            $classes.hide();
            $grades.hide();
            break;
        case 2:
            $students.hide();
            $classes.show();
            $grades.hide();
            break;
        case 3:
            $students.hide();
            $classes.hide();
            $grades.show();
            break;
        default:
            break;
    }
});
var initDateRangePicker = function () {
    page.loadCss(page.plugins.daterangepicker.css);
    $('#daterange').daterangepicker(
        {
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
                monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                firstDay: 1
            },
            ranges: {
                '今天': [
                    moment(),
                    moment()
                ],
                '昨天': [
                    moment().subtract(1, 'days'),
                    moment().subtract(1, 'days')
                ],
                '过去 7 天': [
                    moment().subtract(6, 'days'),
                    moment()
                ],
                '过去 30 天': [
                    moment().subtract(29, 'days'),
                    moment()
                ],
                '这个月': [
                    moment().startOf('month'),
                    moment().endOf('month')
                ],
                '上个月': [
                    moment().subtract(1, 'month').startOf('month'),
                    moment().subtract(1, 'month').endOf('month')
                ]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        },
        function (start, end) {
            $('#daterange').find('span').html(
                start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD')
            );
        }
    );
};
if (typeof moment === 'undefined' && !$.fn.daterangepicker) {
    $.getScript(
        page.siteRoot() + page.plugins.daterangepicker.moment,
        function () {
            $.getScript(
                page.siteRoot() + page.plugins.daterangepicker.js,
                function () { initDateRangePicker(); }
            )
        }
    )
} else { initDateRangePicker(); }
var $stat = $('#stat');
var $range = $('#range');
var $studentId = $('#student_id');
var $classId = $('#class_id');
var $gradeId = $('#grade_id');
var $aConsume = $('#a_consume');
var $consume = $('#consume');
var $charge = $('#charge');
var $aCharge = $('#a_charge');
$stat.on('click', function () {
    if ($range.html().indexOf('fa-calendar') !== -1) {
        page.inform('错误', '请选择日期范围', page.failure);
        return false;
    }
    $('.overlay').show();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: page.siteRoot() + 'consumptions/stat',
        data: {
            '_token': $('#csrf_token').attr('content'),
            'range_id': $rangeId.val(),
            'student_id': $studentId.val() !== null ? $studentId.val() : 0,
            'class_id': $classId.val() !== null ? $classId.val() : 0,
            'grade_id': $gradeId.val() !== null ? $gradeId.val() : 0,
            'date_range': $range.html()
        },
        success: function(result) {
            $('.overlay').hide();
            $aConsume.html(result['consumption']);
            $aCharge.html(result['charge']);
            if (result['consumption'] !== '&yen; 0.00') {
                $consume.show();
            } else {
                $consume.hide();
            }
            if (result['charge'] !== '&yen; 0.00') {
                $charge.show();
            } else {
                $charge.hide();
            }
            page.inform('操作结果', '统计成功', page.success);
        },
        error: function(e) {
            page.errorHandler(e);
        }
    });

    return false;
});
var $detail = $('#detail').find('tbody');
var title =
    '<tr class="text-bold">\n' +
        '<td>#</td>\n' +
        '<td>学生</td>\n' +
        '<td>金额</td>\n' +
        '<td>类型</td>\n' +
        '<td>消费机ID</td>\n' +
        '<td>时间</td>\n' +
        '<td>地点</td>\n' +
    '</tr>';
$consume.on('click', function () { getDetails(0); return false; });
$charge.on('click', function () { getDetails(1); return false; });
var getDetails = function (detail) {
    $('#detail_id').val(detail);
    if (detail === 0) {
        $('.modal-title').addClass('text-red').removeClass('text-green').html('消费明细');
    } else {
        $('.modal-title').addClass('text-green').removeClass('text-red').html('充值明细');
    }
    $('.overlay').show();
    $.when($.ajax({
        type: 'POST',
        dataType: 'json',
        url: page.siteRoot() + 'consumptions/stat?detail=' + detail,
        data: {
            '_token': $('#csrf_token').attr('content'),
            'range_id': $rangeId.val(),
            'student_id': $studentId.val() !== null ? $studentId.val() : 0,
            'class_id': $classId.val() !== null ? $classId.val() : 0,
            'grade_id': $gradeId.val() !== null ? $gradeId.val() : 0,
            'date_range': $range.html()
        },
        success: function (result) {
            $('.overlay').hide();
            $detail.html();
            var rows = title;
            for (var i = 0; i < result['details'].length; i++) {
                rows +=
                    '<tr>' +
                    '<td>' + result['details']['id'] + '</td>' +
                    '<td>' + result['details']['name'] + '</td>' +
                    '<td>' + result['details']['amount'] + '</td>' +
                    '<td>' + result['details']['type'] + '</td>' +
                    '<td>' + result['details']['machineid'] + '</td>' +
                    '<td>' + result['details']['datetime'] + '</td>' +
                    '<td>' + result['details']['location'] + '</td>' +
                    '</tr>';
            }
            $detail.html(rows);
        },
        error: function (e) {
            page.errorHandler(e);
        }
    })).then(function () {
        $('#detail').modal({backdrop: true});
    });
};
var $export = $('#export');
$export.on('click', function () {
    window.location = page.siteRoot() + 'consumptions/export?' +
        'range_id=' + $rangeId.val() +
        '&student_id=' + $studentId.val() !== null ? $studentId.val() : 0 +
        '&class_id=' + $classId.val() !== null ? $classId.val() : 0 +
        '&grade_id=' + $gradeId.val() !== null ? $gradeId.val() : 0 +
        '&date_range=' + encodeURIComponent($range.html()) +
        '&detail=' + $('#detail_id').val();
});
