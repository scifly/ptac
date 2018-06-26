var $rangeId = $('#range_id'),
    $students = $('#students'),
    $classes = $('#classes'),
    $grades = $('#grades');

page.initForm(
    'consumptions',
    'formConsumption',
    'consumptions/stat',
    'POST'
);
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
page.initDateRangePicker();
var $stat = $('#stat'),
    $range = $('#range'),
    $studentId = $('#student_id'),
    $classId = $('#class_id'),
    $gradeId = $('#grade_id'),
    $aConsume = $('#a_consume'),
    $consume = $('#consume'),
    $charge = $('#charge'),
    $aCharge = $('#a_charge');
$stat.on('click', function () {
    if ($range.html().indexOf('fa-calendar') !== -1) {
        page.inform('学生消费记录', '请选择日期范围', page.failure);
        return false;
    }
    $('.overlay').show();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: page.siteRoot() + 'consumptions/stat',
        data: {
            '_token': page.token(),
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
            page.inform(result['title'], result['message'], page.success);
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
            '_token': page.token(),
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
