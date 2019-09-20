var $rangeId = $('#range_id'),
    $students = $('#students'),
    $classes = $('#classes'),
    $grades = $('#grades'),
    $stat = $('#stat'),
    $range = $('#range'),
    $studentId = $('#student_id'),
    $classId = $('#class_id'),
    $gradeId = $('#grade_id'),
    $aConsume = $('#a_consume'),
    $consume = $('#consume'),
    $charge = $('#charge'),
    $aCharge = $('#a_charge'),
    $detail = $('#detail').find('tbody'),
    title = '<tr class="text-bold">\n' +
        '<td>#</td>\n' +
        '<td>学生</td>\n' +
        '<td>金额</td>\n' +
        '<td>类型</td>\n' +
        '<td>消费机ID</td>\n' +
        '<td>时间</td>\n' +
        '<td>地点</td>\n' +
    '</tr>';

page.initForm(
    'consumptions', 'formConsumption',
    'consumptions/stat', 'POST'
);
page.initDateRangePicker();
$rangeId.on('change', function () {
    var rangeId = parseInt($rangeId.val());

    $students.toggle(rangeId === 1);
    $classes.toggle(rangeId === 2);
    $grades.toggle(rangeId === 3);
});
$stat.on('click', function () {
    if ($range.html().indexOf('fa-calendar') !== -1) {
        page.inform('学生消费记录', '请选择日期范围', page.failure);
        return false;
    }
    $('.overlay').show();
    stat('consumptions/stat', function (result) {
        $('.overlay').hide();
        $aConsume.html(result['consumption']);
        $aCharge.html(result['charge']);
        $consume.toggle(result['consumption'] !== '&yen; 0.00');
        $charge.toggle(result['charge'] !== '&yen; 0.00');
        page.inform(result['title'], result['message'], page.success);
    });

    return false;
});
$consume.on('click', function () {
    details(0);
    return false;
});
$charge.on('click', function () {
    details(1);
    return false;
});
$('#export').on('click', function () {
    window.location = page.siteRoot() + 'consumptions/export?' +
        'range_id=' + $rangeId.val() +
        '&student_id=' + $studentId.val() +
        '&class_id=' + $classId.val() +
        '&grade_id=' + $gradeId.val() +
        '&date_range=' + encodeURIComponent($range.html()) +
        '&detail=' + $('#detail_id').val();
});

var details = function (detail) {
    var a = 'text-' + (detail === 0 ? 'red' : 'green'),
        r = 'text-' + (detail === 0 ? 'green' : 'red'),
        h = (detail === 0 ? '消费' : '充值') + '明细';

    $('#detail_id').val(detail);
    $('.modal-title').addClass(a).removeClass(r).html(h);
    $('.overlay').show();
    $.when(
        stat('consumptions/stat?detail=' + detail, function (result) {
            var rows = title, details;

            $('.overlay').hide();
            for (var i = 0; i < result.length; i++) {
                details = result[i]['details'];
                rows += '<tr>' +
                    '<td>' + details['id'] + '</td>' +
                    '<td>' + details['name'] + '</td>' +
                    '<td>' + details['amount'] + '</td>' +
                    '<td>' + details['type'] + '</td>' +
                    '<td>' + details['machineid'] + '</td>' +
                    '<td>' + details['datetime'] + '</td>' +
                    '<td>' + details['location'] + '</td>' +
                '</tr>';
            }
            $detail.html(rows);
        })
    ).then(
        function () {
            $('#detail').modal({backdrop: true});
        }
    );
};
var stat = function (uri, callback) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: page.siteRoot() + uri,
        data: {
            _token: page.token(),
            range_id: $rangeId.val(),
            student_id: $studentId.val(),
            class_id: $classId.val(),
            grade_id: $gradeId.val(),
            date_range: $range.html()
        },
        success: function (result) {
            callback(result);
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
};
