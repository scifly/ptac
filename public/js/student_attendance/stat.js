page.initSelect2();
page.initBackBtn('student_attendances');

page.loadCss(page.plugins.moment.css);
page.loadCss(page.plugins.daterangepicker.css);

/** 加载图表插件 */
$.getMultiScripts([page.plugins.echarts.js], page.siteRoot());

/** 初始化时间范围选择插件 */
$.getMultiScripts([page.plugins.moment.js], page.siteRoot()).done(function () {
    $.getMultiScripts([page.plugins.daterangepicker.js], page.siteRoot()).done(function () {
        $('#reservation').daterangepicker({
            "locale": {
                format: 'YYYY-MM-DD',
                separator: ' ~ ',
                applyLabel: "应用",
                cancelLabel: "取消",
                resetLabel: "重置",
                daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
                monthNames: [
                    '一月', '二月', '三月', '四月', '五月', '六月',
                    '七月', '八月', '九月', '十月', '十一月', '十二月'
                ],
                customRangeLabel: "日历",
            },
            ranges: {
                '最近7日': [
                    moment().subtract(6, 'days'),
                    moment()
                ],
            },
            startDate: moment().subtract(6, 'days'),
            endDate: moment(),
        });
        getdata();
    });
});

/** 初始化年级选择事件监听 */
$.getMultiScripts(['js/contact.select.js'], page.siteRoot()).done(
    function () {
        var cr = $.contactRange();
        cr.onGradeChange('student_attendances', 'stat');
    }
);
var $search = $('#search');
var $token = $('#csrf_token');
$search.click(function () {
    $('#data-table').find('tbody').html('');
    getdata();
});
var $export = $('#export');
$export.on('click', function () {
    window.location = page.siteRoot() + 'student_attendances/export';
});
// helper functions
function getdata() {
    var dates = $('#reservation').val().split('~');
    var days = timeDiff($.trim(dates[0]), $.trim(dates[1])); // 获取总共多少天
    $('.overlay').show();
    $.ajax({
        url: page.siteRoot() + "student_attendances/stat",
        type: 'POST',
        dataType: 'json',
        data: {
            _token: $token.attr('content'),
            class_id: $('#class_id').val(),
            start_time: $.trim(dates[0]),
            end_time: $.trim(dates[1]),
            days: days
        },
        success: function (result) {
            var arrayTime = [];
            arrayTime.length = 0;
            var html = '',
                title = '点击查看详情';
            $.each(result, function (index, obj) {
                var datacon = obj;
                html +=
                    '<tr>' +
                        '<td class="attendances-date">' + datacon['date'] + '</td>' +
                        '<td class="attendances-data" style="font-size: 24px; font-weight: bolder;">' +
                            '<a class="js-show-list normal" ' +
                                'data-toggle="modal" ' +
                                'data-type="normal" ' +
                                'title="' + title + '"' +
                            '>' +
                                datacon['normal'] +
                            '</a> + ' +
                            '<a class="js-show-list abnormal" ' +
                                'data-toggle="modal" ' +
                                'data-type="abnormal" ' +
                                'title="' + title + '"' +
                            '>' +
                                datacon['abnormal'] +
                            '</a> + ' +
                            '<a class="js-show-list missed" ' +
                                'data-toggle="modal" ' +
                                'data-type="missed" ' +
                                'title="' + title + '"' +
                            '>' +
                                datacon['missed'] +
                            '</a> = ' +
                            '<a class="all">' +
                                 datacon['all'] +
                            '</a>' +
                        '</td>' +
                        '<td>' +
                            '<div id="main' + index + '" style="height: 80px; width: 160px; margin: auto;"></div>' +
                        '</td>' +
                    '</tr>';
            });
            $('#data-table ').find('tbody').html(html);
            showDetails();
            $.each(result, function (index, obj) {
                var datacon = obj;
                //饼图数据
                arrayTime.length = 0;
                var sector1 = { value: datacon['normal'],   name: '打卡' };
                var sector2 = { value: datacon['abnormal'], name: '异常' };
                var sector3 = { value: datacon['missed'],  name: '未打' };
                arrayTime.push(sector1);
                arrayTime.push(sector2);
                arrayTime.push(sector3);
                showPieChart(index, arrayTime);
            });
            $('th').addClass('text-center').css('vertical-align', 'middle');
            $('td').addClass('text-center').css('vertical-align', 'middle');
            $('.overlay').hide();
        }
    });
}

function timeDiff(time1, time2) {
    time1 = Date.parse(new Date(time1));
    time2 = Date.parse(new Date(time2));

    return Math.abs(parseInt((time2 - time1) / 1000 / 3600 / 24) + 1);
}

function showPieChart(index, arrayTime) {
    var myChart = echarts.init(document.getElementById('main' + index));
    var option = {
        title: { text: '打卡详情' },
        color: ['#83db74', '#fdde52', '#fc7f4e'],
        series: [
            {
                name: '访问来源',
                type: 'pie',
                radius: '50%',
                data: arrayTime,
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    };
    myChart.setOption(option);
}

function showDetails() {
    $('.js-show-list').on('click', function () {
        $('.overlay').show();
        $.when($.ajax({
            url: page.siteRoot() + "student_attendances/detail",
            type: 'POST',
            data: {
                _token: $token.attr('content'),
                class_id: $('#class_id').val(),
                type: $(this).attr('data-type'),
                date: $(this).parent().prev().text()
            },
            success: function (result) {
                var html = '';
                for (var i = 0; i < result.length; i++) {
                    var data = result[i];
                    html +=
                        '<tr>' +
                            '<td>' + data['name'] + '</td>' +
                            '<td>' + data['custodian'] + '</td>' +
                            '<td>' + data['mobile'] + '</td>' +
                            '<td>' + data['punch_time'] + '</td>' +
                            '<td>' + data['inorout'] + '</td>' +
                        '</tr>';
                }
                $('#student-table').find('tbody').html(html);
                $('td').addClass('text-center');
                $('.overlay').hide();
            },
            error: function (e) {
                page.errorHandler(e);
            }
        })).then(function () {
            $('#student-list').modal({ backdrop: true});
        });
    });
}
