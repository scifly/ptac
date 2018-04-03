<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta name="csrf_token" content="{{ csrf_token() }}" id="csrf_token">
    <title>学生列表</title>
    <link rel="stylesheet" href="{{ URL::asset('css/weui.min.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/jquery-weui.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/wechat/icon/iconfont.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/wechat/attendance/custodian.css') }}">
</head>
<body ontouchstart>
<div class="main">
    <div class="list">
        @if (!empty($students))
            @foreach($students as $s)
                <div class="list-item">
                    <div class="list-item-info">
                        <div class="username">姓名 : <span>{{ $s->studentname }}</span></div>
                        <div class="school">学校 : <span>{{ $s->schoolname }}</span></div>
                        <div class="grade">班级 : <span>{{ $s->class_id }}</span></div>
                    </div>
                    <div class="line"></div>
                    <table class="kaoqin-tongji">
                        <tr>
                            <td>
                                <div class="kaoqin-date-circle okstatus"></div>
                                <span class="pl10">正常:</span>
                                <span>{{ $s->normal }}天</span>
                            </td>
                            <td>
                                <div class="kaoqin-date-circle notstatus"></div>
                                <span class="pl10">异常:</span>
                                <span>{{ $s->abnormal }}天</span>
                            </td>
                            <td>
                                <div class="kaoqin-date-circle reststatus"></div>
                                <span class="pl10">请假:</span>
                                <span>0天</span>
                            </td>
                        </tr>
                    </table>
                    <div class="list-item-icon">
                        <a href="{{ url('attendance/' . $s->id) }}">
                            <i class="icon iconfont icon-jiantouyou"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
<script src="{{URL::asset('js/jquery.min.js')}}"></script>
<script src="{{URL::asset('js/fastclick.js')}}"></script>
<script src="{{URL::asset('js/jquery-weui.min.js')}}"></script>
<script>FastClick.attach(document.body);</script>
</body>
</html>
