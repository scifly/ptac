<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta name="csrf_token" content="{{ csrf_token() }}" id="csrf_token">
    <title>考勤记录</title>
    <link rel="stylesheet" href="{{ URL::asset('css/weui.min.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/jquery-weui.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/wechat/icon/iconfont.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/wechat/attendance/detail.css') }}">
</head>
<body ontouchstart>
<div class="multi-role">
    <div class="switchschool-item clearfix">
        <div class="switchschool-head">
            <div class="title-name"> 校园大学</div>
        </div>
    </div>
    <div class="kaoqin-history-calender">
        <div id="inline-calendar"></div>
        <table class="kaoqin-tongji js-kaoqin-tongji">
            <tbody>
            <tr>
                <td>
                    <div class="kaoqin-date-circle okstatus"></div>
                    <span class="pl10">正常:</span>
                    <span>{{ count($data['nDays']) }}天</span>
                </td>
                <td>
                    <div class="kaoqin-date-circle notstatus"></div>
                    <span class="pl10">异常:</span>
                    <span>{{ count($data['aDays']) }}天</span>
                </td>
                <td>
                    <div class="kaoqin-date-circle reststatus"></div>
                    <span class="pl10">请假:</span>
                    <span>0天</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="kaoqin-day-detail js-kaoqin-day-detail">
        <div class="js-kaoqin-detail-date kaoqin-detail-date">
            {{ $date }}
        </div>
        @foreach($ins as $in)
            <div class="mt20 history-list-con" style="">
                @if(sizeof($into) != 0)
                    <span class="js-kaoqin-status-morning"
                          style="display:inline-block">{{ $i->studentAttendancesetting->name }}</span>
                    <span class="kaoqin-detail-status c-83db74">{{ $in->status == 1 ? '正常' : '异常' }}</span>
                    <span class="time">{{ substr($in->punch_time, 11) }}</span>
                    {{--@else--}}
                    {{--<span class="js-kaoqin-status-morning" style="display:inline-block">暂无数据</span>--}}
                    {{--<span class="kaoqin-detail-status c-83db74">{{ '暂无数据' }}</span>--}}
                    {{--<span class="time">暂无数据</span>--}}
                @endif
            </div>
        @endforeach
        @foreach($outs as $out)
            <div class="mt20 history-list-con" style="">
                @if(sizeof($outs) != 0)
                    <span class="js-kaoqin-status-morning" style="display:inline-block">下班</span>
                    <span class="kaoqin-detail-status c-83db74">{{ $out->status == 1 ?'正常' : '异常' }}</span>
                    <span class="time">{{ substr($out->punch_time,11) }}</span>
                @endif
            </div>
        @endforeach
    </div>
</div>
<script src="{{URL::asset('js/jquery.min.js')}}"></script>
<script src="{{URL::asset('js/fastclick.js')}}"></script>
<script src="{{URL::asset('js/jquery-weui.min.js')}}"></script>
<script>
    var days = $.parseJSON('{{$days}}'.replace(/&quot;/g, '"')),
        token = $('#csrf_token').attr('content'),
        id = '{{ $id }}',
        nDays = days['nDays'],
        aDays = days['aDays'],
        months = {
            '一月': '01', '二月': '02', '三月': '03', '四月': '04', '五月': '05', '六月': '06',
            '七月': '07', '八月': '08', '九月': '09', '十月': '10', '十一月': '11', '十二月': '12',
        };

    FastClick.attach(document.body);

    $("#inline-calendar").calendar({
        container: "#inline-calendar",
    });

    var $year = $('.current-year-value'),
        $month = $('.current-month-value'),
        sYear = $year.text(),
        sMonth = $month.text(),
        iMonth = parseInt(months[sMonth]);

    $.each(nDays, function () {
        var date = sYear + '-' + (iMonth - 1) + '-' + parseInt(this.substring(8, 10));
        $("[data-date = " + date + "]").addClass('picker-calendar-day-normal')
    });
    $.each(aDays, function () {
        var date = sYear + '-' + (iMonth - 1) + '-' + parseInt(this.substring(8, 10));
        $("[data-date = " + date + "]").addClass('picker-calendar-day-abnormal')
    });

    $('.picker-calendar-year-picker a').click(function () {
        var sYear = $year.html(),
            sMonth = $month.html();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'detail',
            data: {
                id: id,
                ym: sYear + '-' + months[sMonth],
                _token: token
            },
            success: function (result) {
                var str = '';

                $.each(result['data']['nDays'], function () {
                    var date = sYear + '-' + (parseInt(months[sMonth]) - 1) + '-' + parseInt(this.substring(8, 10));
                    $("[data-date = " + date + "]").addClass('picker-calendar-day-normal');
                });
                $.each(result['data']['aDays'], function () {
                    var date = y + '-' + (parseInt(months[sMonth]) - 1) + '-' + parseInt(this.substring(8, 10));
                    $("[data-date = " + date + "]").addClass('picker-calendar-day-abnormal');
                });
                $('.picker-calendar-month-current .picker-calendar-day').eq(11).addClass('picker-calendar-day-leave');
                str +=
                    '<tr>' +
                        '<td>' +
                            '<div class="kaoqin-date-circle okstatus"></div>' +
                            '<span class="pl10">正常:</span>' +
                            '<span>' + result['data']['nSum'] + '天</span>' +
                        '</td>' +
                        '<td>' +
                            '<div class="kaoqin-date-circle notstatus"></div>' +
                            '<span class="pl10">异常:</span>' +
                            '<span>' + result['data']['aSum'] + '天</span>' +
                        '</td>' +
                        '<td>' +
                            '<div class="kaoqin-date-circle reststatus"></div>' +
                            '<span class="pl10">请假:</span>' +
                            '<span>0 天</span>' +
                        '</td>' +
                    '</tr>';
                $('.kaoqin-tongji tbody').html(str);
            }
        });
    });

    // 点击月份
    $('.picker-calendar-month-picker a').click(function () {
        var sYear = $('.current-year-value').html(),
            sMonth = $('.current-month-value').html(),
            iMonth = parseInt(months[sMonth]);
        var years = sYear + '-' + sMonth;
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: 'detail',
            data: {
                id: id,
                years: years,
                _token: $('#csrf_token').attr('content')
            },
            success: function (result) {
                var str = '';

                $.each(result['date']['nDays'], function () {
                    var tmp = sYear + '-' + (iMonth - 1) + '-' + parseInt((this.substring(8, 10)));
                    $("[data-date = " + tmp + " ]").addClass('picker-calendar-day-normal');
                });
                $.each(result['date']['aDays'], function () {
                    var tmp = sYear + '-' + (iMonth - 1) + '-' + parseInt((this.substring(8, 10)));
                    $("[data-date = " + tmp + " ]").addClass('picker-calendar-day-abnormal');
                });
                str +=
                    '<tr>' +
                        '<td>' +
                            '<div class="kaoqin-date-circle okstatus"></div>' +
                            '<span class="pl10">正常:</span>' +
                            '<span>' + result['date']['nSum'] + ' 天</span>' +
                        '</td>' +
                        '<td>' +
                            '<div class="kaoqin-date-circle notstatus"></div>' +
                            '<span class="pl10">异常:</span>' +
                            '<span>' + result['date']['aSum'] + ' 天</span>' +
                        '</td>' +
                        '<td>' +
                            '<div class="kaoqin-date-circle reststatus"></div>' +
                            '<span class="pl10">请假:</span>' +
                            '<span>0天</span>' +
                        '</td>' +
                    '</tr>';
                $('.kaoqin-tongji tbody').html(str);
            }
        });

    });

    // 点击日期
    $('.picker-calendar-day').click(function () {
        var year = $(this).attr('data-year'), 
            month = pad(parseInt($(this).attr('data-month')) + 1, 2),
            day = pad($(this).attr('data-day'), 2);
        
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: 'detail',
            data: {
                id: id,
                date: year + '-' + month + '-' + day,
                _token: token
            },
            success: function (result) {
                var str = '',
                    status = '';

                str += '<div class="js-kaoqin-detail-date kaoqin-detail-date">' + result['date'] + '</div>';
                if (result['ins'].length > 0) {
                    for (var i = 0; i < result['ins'].length; i++) {
                        status = result['ins'][i]['status'] === 1 ? '正常' : '异常';
                        str +=
                            '<div class="mt20 history-list-con" style="">' +
                                '<span class="js-kaoqin-status-morning" style="display:inline-block">上班</span>' +
                                '<span class="kaoqin-detail-status c-83db74">' + status + '</span>' +
                                '<span class="time">' + result['ins'][i]['punch_time'] + '</span>' +
                            '</div>';
                    }
                } else {
                    str +=
                        '<div class="mt20 history-list-con" style="">' +
                            '<span class="js-kaoqin-status-morning" style="display:inline-block">上班</span>' +
                            '<span class="kaoqin-detail-status c-83db74">' + '暂无数据' + '</span>' +
                            '<span class="time">' + '暂无' + '</span>' +
                        '</div>';
                }
                if (result['outs'].length > 0) {
                    for (var j = 0; j < result['outs'].length; j++) {
                        var out = result['outs'][j];
                        str += ' <div class="mt20 history-list-con" style="">' +
                            '<span class="js-kaoqin-status-morning" style="display:inline-block">下班</span>';
                        if (out.status === 1) {
                            str += '<span class="kaoqin-detail-status c-83db74">' + '正常' + '</span>';
                        } else {
                            str += '<span class="kaoqin-detail-status c-83db74">' + '异常' + '</span>';

                        }
                        str += '<span class="time">' + out.punch_time + '</span>' +
                            '</div>';
                    }
                } else {
                    str += ' <div class="mt20 history-list-con" style="">' +
                        '<span class="js-kaoqin-status-morning" style="display:inline-block">下班</span>' +
                        '<span class="kaoqin-detail-status c-83db74">' + '暂无数据' + '</span>' +
                        '<span class="time">' + '暂无' + '</span>' +
                        '</div>';
                }
                $('.kaoqin-day-detail').html(str)
            }
        });
    });

    function pad(n, width, z) {
        z = z || '0';
        n = n + '';
        return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
    }
</script>
</body>
</html>
