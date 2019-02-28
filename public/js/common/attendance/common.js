//# sourceURL=common.js
(function ($) {
    $.attendance = function (options) {
        var attend = {
            options: $.extend({}, options),
            stat: function (table) {
                var $range = $('#range'),
                    dates = $range.html().split(' - '),
                    data = {
                        _token: page.token(),
                        start_date: $.trim(dates[0]),
                        end_date: $.trim(dates[1])
                    };
                if (table === 'student_attendances') {
                    data['class_id'] = $('#class_id').val();
                }
                if ($range.html().indexOf('fa-calendar') !== -1) {
                    page.inform('考勤统计', '请选择日期范围', page.failure);
                    return false;
                }
                $('.overlay').show();
                $.ajax({
                    url: page.siteRoot() + table + '/stat',
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        var pies = [], html = '', title = '点击查看详情';
                        $.each(result, function (index, obj) {
                            html +=
                                '<tr>' +
                                    '<td class="attendances-date">' + obj['date'] + '</td>' +
                                    '<td class="attendances-data" style="font-size: 24px; font-weight: bolder;">' +
                                        '<a class="js-show-list normal" ' +
                                            'data-toggle="modal" ' +
                                            'data-type="normal" ' +
                                            'title="' + title + '"' +
                                        '>' +
                                            obj['normal'] +
                                        '</a> + ' +
                                        '<a class="js-show-list abnormal" ' +
                                            'data-toggle="modal" ' +
                                            'data-type="abnormal" ' +
                                            'title="' + title + '"' +
                                        '>' +
                                            obj['abnormal'] +
                                        '</a> + ' +
                                        '<a class="js-show-list missed" ' +
                                            'data-toggle="modal" ' +
                                            'data-type="missed" ' +
                                            'title="' + title + '"' +
                                        '>' +
                                            obj['missed'] +
                                        '</a> = ' +
                                        '<a class="all">' +
                                            obj['all'] +
                                        '</a>' +
                                    '</td>' +
                                    '<td>' +
                                        '<div id="main' + index + '" style="height: 80px; width: 160px; margin: auto;"></div>' +
                                    '</td>' +
                                '</tr>';
                        });
                        $('#results').find('tbody').html(html);
                        attend.detail(table);
                        $.each(result, function (index, obj) {
                            //饼图数据
                            pies.length = 0;
                            var sector1 = { value: obj['normal'],   name: '打卡' };
                            var sector2 = { value: obj['abnormal'], name: '异常' };
                            var sector3 = { value: obj['missed'],  name: '未打' };
                            pies.push(sector1);
                            pies.push(sector2);
                            pies.push(sector3);
                            attend.chart(index, pies);
                        });
                        $('td').addClass('text-center').css('vertical-align', 'middle');
                        $('.overlay').hide();
                    },
                    error: function (e) {
                        page.errorHandler(e);
                    }
                });
            },
            chart: function (index, pies) {
                var pie = echarts.init(document.getElementById('main' + index));
                var option = {
                    title: { text: '打卡详情' },
                    color: ['#83db74', '#fdde52', '#fc7f4e'],
                    series: [
                        {
                            name: '访问来源',
                            type: 'pie',
                            radius: '50%',
                            data: pies,
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
                pie.setOption(option);
            },
            detail: function (table) {
                $('.js-show-list').on('click', function () {
                    $('.overlay').show();
                    $.when($.ajax({
                        url: page.siteRoot() + table + '/detail',
                        type: 'POST',
                        data: {
                            _token: page.token(),
                            class_id: $('#class_id').val(),
                            type: $(this).attr('data-type'),
                            date: $(this).parent().prev().text()
                        },
                        success: function (result) {
                            var html = '';
                            for (var i = 0; i < result.length; i++) {
                                if (table === 'student_attendances') {
                                    html +=
                                        '<tr>' +
                                            '<td>' + result[i]['name'] + '</td>' +
                                            '<td>' + result[i]['custodian'] + '</td>' +
                                            '<td>' + result[i]['mobile'] + '</td>' +
                                            '<td>' + result[i]['punch_time'] + '</td>' +
                                            '<td>' + result[i]['direction'] + '</td>' +
                                        '</tr>';
                                } else {
                                    html +=
                                        '<tr>' +
                                            '<td>' + result[i]['name'] + '</td>' +
                                            '<td>' + result[i]['mobile'] + '</td>' +
                                            '<td>' + result[i]['punch_time'] + '</td>' +
                                            '<td>' + result[i]['direction'] + '</td>' +
                                            '<td>' + result[i]['status'] + '</td>' +
                                        '</tr>';
                                }
                            }
                            $('#records').find('tbody').html(html);
                            $('td').addClass('text-center');
                            $('.overlay').hide();
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    })).then(function () {
                        $('#detail').modal({ backdrop: true});
                    });
                });
            }
        };

        return { stat: attend.stat };
    }
})(jQuery);