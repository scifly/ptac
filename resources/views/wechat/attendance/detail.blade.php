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
    <link rel="stylesheet" href="{{ URL::asset('squad.css') }}">
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
        {{ Form::hidden('id', $id, ['id' => $id]) }}
        <table class="kaoqin-tongji js-kaoqin-tongji">
            <tbody>
            <tr>
                <td>
                    <div class="kaoqin-date-circle okstatus"></div>
                    <span class="pl10">正常: </span>
                    <span>{{ $data['nSum'] }} 天</span>
                </td>
                <td>
                    <div class="kaoqin-date-circle notstatus"></div>
                    <span class="pl10">异常: </span>
                    <span>{{ $data['aSum'] }} 天</span>
                </td>
                <td>
                    <div class="kaoqin-date-circle reststatus"></div>
                    <span class="pl10">请假: </span>
                    <span>0 天</span>
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
                @if (sizeof($ins) != 0)
                    <span class="js-kaoqin-status-morning"
                          style="display:inline-block">{{ $in->studentAttendancesetting->name }}
                    </span>
                    <span class="kaoqin-detail-status c-83db74">{{ $in->status ? '正常' : '异常' }}</span>
                    <span class="time">{{ date('H:i:s', strtotime($in->punch_time)) }}</span>
                @endif
            </div>
        @endforeach
        @foreach ($outs as $out)
            <div class="mt20 history-list-con" style="">
                @if (sizeof($outs) != 0)
                    <span class="js-kaoqin-status-morning" style="display:inline-block">放学</span>
                    <span class="kaoqin-detail-status c-83db74">{{ $out->status ? '正常' : '异常' }}</span>
                    <span class="time">{{ date('H:i:s', strtotime($out->punch_time)) }}</span>
                @endif
            </div>
        @endforeach
    </div>
</div>
<script src="{{URL::asset('js/jquery.min.js')}}"></script>
<script src="{{URL::asset('js/fastclick.js')}}"></script>
<script src="{{URL::asset('js/jquery-weui.min.js')}}"></script>
<script src="{{URL::asset('js/wechat/attendance/detail.js')}}"></script>
</body>
</html>
