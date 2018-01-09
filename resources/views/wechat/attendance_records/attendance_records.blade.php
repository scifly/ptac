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

    <style>
        body, html {
            height: 100%;
            width: 100%;
            overflow-x: hidden;
        }
        body{
            margin: 0;
            padding:0;
            background-color: #fff;
            font-family: "微软雅黑";
            font-size: 14px;
        }
        .multi-role {
            background: #fff;
            position: relative;
        }
        .multi-role .switchschool-item {
            padding: 5px 10px;
            padding-right: 0;
            display: -webkit-box;
            line-height: 30px;
            border-bottom: 5px solid #f8f8f8;
            text-align: center;
        }
        .multi-role .switchschool-item .switchschool-title{
            -webkit-box-flex: 1;position: relative;
        }
        .title-name{
            font-size: 16px;
            color: #686868;
            width: 100%;
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            float: left;
        }
        .switchschool-head{
            width: 100%;
        }
        .picker-calendar-day.picker-calendar-day-selected span {
            background: #fff;
            border: 2px solid #83db74;
            color: #333;
        }
        .picker-calendar-day.picker-calendar-day-normal span {
            background-color: #83db74;
        }
        .picker-calendar-day.picker-calendar-day-abnormal span {
            background-color: #fdde52;
        }
        .picker-calendar-day.picker-calendar-day-leave span {
            background-color: #fc7f4e;
        }

        .kaoqin-history-calender .kaoqin-tongji {
            width: 94%;
            margin-left: 6%;
        }
        .kaoqin-history-calender .kaoqin-tongji td {
            display: inline-block;
            width: 32%;
            margin: 16px 0px;
        }

        .kaoqin-history-calender .kaoqin-date-circle {
            float: left;
            margin-top: 2px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #ccc;
        }
        .pl10 {
            padding-left: 10px;
        }
        .kaoqin-history-calender .okstatus {
            background-color: #83db74;
        }
        .kaoqin-history-calender .notstatus {
            background-color: #fdde52;
        }
        .kaoqin-history-calender .reststatus {
            background-color: #fc7f4e;
        }
        .kaoqin-day-detail {
            width: 100%;
            min-height: 140px;
            padding-bottom: 40px;
        }
        .kaoqin-detail-date {
            color: #666;
            height: 29px;
            line-height: 29px;
            padding-left: 8%;
            background-color: #f0f0f0;
        }
        .kaoqin-day-detail .history-list-con {
            padding-left: 8%;
            position: relative;
        }
        .mt20 {
            margin-top: 20px;
        }
        .kaoqin-day-detail .history-list-con .kaoqin-detail-status {
            position: absolute;
            top: 0;
            left: 30%;
        }
        .kaoqin-day-detail .history-list-con .time{
            position: absolute;
            top: 0;
            left: 60%;
            color: #888;
        }
        .c-83db74 {
            color: #83db74;
        }
    </style>
    <head>
<body ontouchstart>
<div class="multi-role">
    <div class="switchschool-item clearfix">
        <div class="switchschool-head">
            <div class="title-name" > 校园大学 </div>
        </div>
    </div>

    <div class="kaoqin-history-calender">
        <div id="inline-calendar">

        </div>

        <table class="kaoqin-tongji js-kaoqin-tongji">
            <tr>
                <td>
                    <div class="kaoqin-date-circle okstatus"></div>
                    <span class="pl10">正常:</span>
                    <span>{{ count($data['adays']) }}天</span>
                </td>

                <td>
                    <div class="kaoqin-date-circle notstatus"></div>
                    <span class="pl10">异常:</span>
                    <span>{{ count($data['ndays']) }}天</span>
                </td>

                <td>
                    <div class="kaoqin-date-circle reststatus"></div>
                    <span class="pl10">请假:</span>
                    <span>0天</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="kaoqin-day-detail js-kaoqin-day-detail">
        <div class="js-kaoqin-detail-date kaoqin-detail-date">
            {{ $time }}
        </div>
        <div class="mt20 history-list-con" style="">
            <span class="js-kaoqin-status-morning" style="display:inline-block">上班</span>
            @if(sizeof($into) != 0)
            <span class="kaoqin-detail-status c-83db74">{{ $into[0]->status == 1 ? '正常' : '异常' }}</span>
            <span class="time">{{ $into[0]->punch_time }}</span>
                @else
                <span class="kaoqin-detail-status c-83db74">{{ '暂无数据' }}</span>
                <span class="time">暂无数据</span>
            @endif
        </div>
        <div class="mt20 history-list-con" style="">
            <span class="js-kaoqin-status-morning" style="display:inline-block">下班</span>
            @if(sizeof($out) != 0)
            <span class="kaoqin-detail-status c-83db74">{{ $out[0]->status == 1 ?'正常' : '异常' }}</span>
            <span class="time">{{ $out[0]->punch_time }}</span>
            @endif
        </div>
    </div>
</div>

<script src="{{URL::asset('js/jquery.min.js')}}"></script>
<script src="{{URL::asset('js/fastclick.js')}}"></script>

<script>
    $(function() {
        FastClick.attach(document.body);
    });
</script>
<script src="{{URL::asset('js/jquery-weui.min.js')}}"></script>
<script>
    var days =$.parseJSON('{{$days}}'.replace(/&quot;/g,'"'));
    var id = '{{ $id }}';
    var ndays = days.ndays;
    var adays = days.adays;

</script>
<script>
    showdata();
    function showdata(){
        $("#inline-calendar").calendar({
            container: "#inline-calendar",
        });

    }
    for(var i in ndays){
        $('.picker-calendar-month-current .picker-calendar-day').eq(ndays[i].substring(8,10)-1).addClass('picker-calendar-day-normal');
    }
    for(var j in adays)
    {
        $('.picker-calendar-month-current .picker-calendar-day').eq(adays[j].substring(8,10)-1).addClass('picker-calendar-day-abnormal');
    }
    $('.picker-calendar-month-current .picker-calendar-day').eq(1).addClass('picker-calendar-day-leave');

    $('.picker-calendar-month-current .picker-calendar-day').click(function () {

    });

    // 点击年份
    $('.picker-calendar-year-picker a').click(function () {

    });

    $('.picker-calendar-month-picker a').click(function () {
        console.log(1);

        var year = $('.current-year-value').html();
        console.log(year);
    });

    // 点击日期
    $('.picker-calendar-day').click(function () {
        var year = $(this).attr('data-year');
        var month = parseInt($(this).attr('data-month'))+1;
        if(month<10){
            month = '0'+month;
        }
        var day = $(this).attr('data-day');
        if(day < 10){
            day = '0'+day;
        }
        var date = year+'-'+month+'-'+day;
        $.ajax({
            type:'post',
            dataType:'json',
            url: 'attendance_records',
            data: { id:id,date:date,_token:$('#csrf_token').attr('content')},
            success: function ($data) {
                var str = '';
                var time = $data.time;
                str += ' <div class="js-kaoqin-detail-date kaoqin-detail-date">' + time +
                    '</div>';
                if ($data.into.length > 0) {
                    for (var i = 0; i < $data.into.length; i++) {
                        var into = $data.into[i];
                        str += ' <div class="mt20 history-list-con" style="">' +
                            '<span class="js-kaoqin-status-morning" style="display:inline-block">上班</span>';
                        if (into.status === 1) {
                            str += '<span class="kaoqin-detail-status c-83db74">' + '正常' + '</span>';
                        } else {
                            str += '<span class="kaoqin-detail-status c-83db74">' + '异常' + '</span>';

                        }
                        str += '<span class="time">' + into.punch_time + '</span>' +
                            '</div>';
                    }
                } else {
                    str += ' <div class="mt20 history-list-con" style="">' +
                        '<span class="js-kaoqin-status-morning" style="display:inline-block">上班</span>' +
                        '<span class="kaoqin-detail-status c-83db74">' + '暂无数据' + '</span>' +
                        '<span class="time">' + '暂无' + '</span>' +
                        '</div>';
                }
                if ($data.out.length > 0) {
                    for (var j = 0; j < $data.out.length; j++) {
                        var out = $data.out[j];
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

</script>
</body>
</html>
