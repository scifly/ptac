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
    <style>
        body, html {
            height: 100%;
            width: 100%;
            overflow-x: hidden;
        }
        body{
            margin: 0;
            padding:0;
            background-color: #f2f2f2;
            font-family: "微软雅黑";
        }
        .main{
            height: 100%;
            width: 100%;
        }
        .list{
            padding: 15px;
        }
        .list-item{
            width: 100%;
            border:1px solid #ddd;
            min-height: 170px;
            background-color: #fff;
            border-radius: 5px;
            position: relative;
            color: #333;
            margin-bottom: 15px;
        }
        .list-item .username,.list-item .school,.list-item .grade{
            padding:2px 20px;
            font-size: 15px;
        }
        .list-item .username{
            font-size: 18px;
            margin-top: 10px;
            /*padding: 20px;*/
        }
        .list-item .school,.list-item .grade{
            color: #777;
            margin-top: 3px;
        }
        .list-item .line{
            height: 1px;
            width: 100%;
            background-color: #e8e8e8;
            margin-top: 15px;
        }

        .kaoqin-tongji {
            width: 100%;
            margin-left:5%;
            /*text-align: center;*/
        }
        .kaoqin-tongji td {
            display: inline-block;
            width: 32%;
            height: 48px;
            line-height: 48px;
            font-size: 14px;
        }
        .kaoqin-date-circle {
            float: left;
            margin-top: 2px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #ccc;
            margin-top: 17px;
            margin-right: 2px;
        }
        .okstatus {
            background-color: #83db74;
        }
        .notstatus {
            background-color: #fdde52;
        }
        .reststatus {
            background-color: #fc7f4e;
        }
        .list-item .list-item-icon{
            position: absolute;
            height: 94px;
            line-height: 94px;
            top: 12px;
            right: 15px;
        }
        .list-item .list-item-icon i{
            font-size: 30px;
            color: #aaa;
        }
    </style>
    <head>
<body ontouchstart>
<div class="main">
    <div class="list">
        @if( sizeof($students) != 0)
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
                                <span>{{$s->normal}}天</span>
                            </td>

                            <td>
                                <div class="kaoqin-date-circle notstatus"></div>
                                <span class="pl10">异常:</span>
                                <span>{{$s->abnormal }}天</span>
                            </td>

                            <td>
                                <div class="kaoqin-date-circle reststatus"></div>
                                <span class="pl10">请假:</span>
                                <span>0天</span>
                            </td>
                        </tr>
                    </table>

                    <div class="list-item-icon">
                        <a href="{{ url('attendance_records/'.$s->id) }}">
                            <i class="icon iconfont icon-jiantouyou"></i>
                        </a>
                    </div>

                </div>
            @endforeach
        @endif

        {{--<div class="list-item">--}}
            {{--<div class="list-item-info">--}}
                {{--<div class="username">姓名 : <span>张三</span></div>--}}
                {{--<div class="school">学校 : <span>希望小学</span></div>--}}
                {{--<div class="grade">班级 : <span>三年级三班</span></div>--}}
            {{--</div>--}}
            {{--<div class="line"></div>--}}

            {{--<table class="kaoqin-tongji">--}}
                {{--<tr>--}}
                    {{--<td>--}}
                        {{--<div class="kaoqin-date-circle okstatus"></div>--}}
                        {{--<span class="pl10">正常:</span>--}}
                        {{--<span>14天</span>--}}
                    {{--</td>--}}

                    {{--<td>--}}
                        {{--<div class="kaoqin-date-circle notstatus"></div>--}}
                        {{--<span class="pl10">异常:</span>--}}
                        {{--<span>0天</span>--}}
                    {{--</td>--}}

                    {{--<td>--}}
                        {{--<div class="kaoqin-date-circle reststatus"></div>--}}
                        {{--<span class="pl10">请假:</span>--}}
                        {{--<span>0天</span>--}}
                    {{--</td>--}}
                {{--</tr>--}}
            {{--</table>--}}

            {{--<div class="list-item-icon">--}}
                {{--<a><i class="icon iconfont icon-jiantouyou"></i></a>--}}
            {{--</div>--}}

        {{--</div>--}}


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

</script>
</body>
</html>
