<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta name="csrf_token" content="{{ csrf_token() }}" id="csrf_token">
    <title>班级考试列表</title>
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
        }
        a{
            color: #333;
        }
        ::-webkit-scrollbar {
            width: 0em;
        }
        ::-webkit-scrollbar:horizontal {
            height: 0em;
        }
        .main{
            height: 100%;
            width: 100%;
            background-color: #fff;
        }
        .header{
            position: fixed;top: 0;z-index: 999;width: 100%;background-color: #fff
        }
        .multi-role {
            background: #fff;
            position: relative;
            height: 100%;
            overflow-y: auto;
        }
        .multi-role .switchclass-item {
            padding: 0px;
            padding-right: 0;
            display: -webkit-box;
            line-height: 20px;
            border-bottom:0;
            text-align: center;
        }
        .multi-role .switchclass-item .switchclass-title{
            -webkit-box-flex: 1;position: relative;
        }
        .title-name{
            font-size: 18px;
            color: #686868;
            width: 100%;
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            float: left;
        }
        .switchclass-head{
            width: 100%;
        }
        .weui-cell__bd{
            width:65%;
        }
        .weui-cell__bd p{
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width:100%;
        }
        .time{
            width: 35%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .loadmore{
            text-align: center;
            height: 40px;
            line-height: 40px;
        }
        .loadmore i{
            font-size: 16px;
            margin-top: -3px;
            margin-right: 10px;
        }

    </style>
    <head>
<body ontouchstart>

<div class="multi-role">
    <div class="switchclass-item clearfix">
        <div class="switchclass-head">

            <div class="weui-cell">
                <div class="weui-cell__bd title-name">
                    <input style="text-align: center;" id="classlist" class="weui-input" type="text" value="@if(!empty($scores)) {{$scores[0]['classname']}} @endif"
                           readonly="" data-values="{{$scores[0]['class_id']}}">
                </div>
            </div>

            <!--<input class="title-name" id="classlist" type="text" value="一年级一班" readonly="" data-values="一年级一班">-->
        </div>
    </div>
    <div class="weui-search-bar" id="searchBar">
        <form class="weui-search-bar__form" action="#">
            <div class="weui-search-bar__box">
                <i class="weui-icon-search"></i>
                <input type="search" class="weui-search-bar__input" id="searchInput" placeholder="搜索" required="">
                <a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
            </div>
            <label class="weui-search-bar__label" id="searchText" style="transform-origin: 0px 0px 0px; opacity: 1; transform: scale(1, 1);">
                <i class="weui-icon-search"></i>
                <span>搜索</span>
            </label>
        </form>
        <a href="javascript:" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
    </div>

    <!--列表-->
    <div class="weui-cells" style="margin-top: 89px;">
        @foreach($scores as $s)
            <a class="weui-cell weui-cell_access" href='{{ url("wechat/score/detail?examId=".$s['id']."&classId=".$s['class_id']) }}'>
                <div class="weui-cell__bd">
                    <p>{{ $s['name'] }}</p>
                </div>
                <div class="weui-cell__ft time">{{ $s['start_date'] }}</div>
            </a>
        @endforeach
    </div>

    <div class="loadmore">
        <span class="weui-loadmore__tips"><i class="icon iconfont icon-shuaxin"></i>加载更多 </span>
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
    var className = $.parseJSON('{{$className}}'.replace(/&quot;/g,'"'));
    var pageSize = '{{$pageSize}}';
    //班级列表
    $("#classlist").select({
        title: "选择班级",
        items: className
    });

    $("#classlist").on('change',function () {
        $('.loadmore').show();
        var class_id = $(this).attr('data-values');
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: 'score_lists',
            data: {class_id: class_id, _token: $('#csrf_token').attr('content')},
            success: function ($data) {
                var html = '';
                if($data.data.length !== 0)
                {
                    for(var j=0 ; j< $data.data.length; j++)
                    {
                        var data = $data.data[j];
                        html += '<a class="weui-cell weui-cell_access" href="wechat/score/detail?examId='+data.id+'&classId='+class_id+'">' +
                            '<div class="weui-cell__bd">' +
                            '<p>'+data.name +'</p>' +
                            '</div>' +
                            '<div class="weui-cell__ft time">'+ data.start_date+'</div>' +
                            '</a>';
                    }
                    $('.weui-cells').html(html);
                }else{
                    $('.weui-cells').html('');
                    $('.loadmore').hide();

                }
            }
        });
    });

    var start = 0;
    $('.loadmore').click(function () {
        start++;

        loadmore(start);
    });

    function loadmore() {
        var class_id = $('input').attr('data-values');
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: 'score_lists',
            data: {start: start, class_id: class_id, _token: $('#csrf_token').attr('content')},
            success: function ($data) {
                var html = '';
                if($data.data.length !== 0)
                {
                    for(var i=0; i< $data.data.length;i++)
                    {
                        var score = $data.data[i];
                        html += '<a class="weui-cell weui-cell_access" href="wechat/score/detail?examId='+score.id+'&classId='+class_id+'">' +
                            '<div class="weui-cell__bd">' +
                            '<p>'+score.name +'</p>' +
                            '</div>' +
                            '<div class="weui-cell__ft time">'+ score.start_date+'</div>' +
                            '</a>';
                    }
                    $('.weui-cells').append(html);
                }else{

                    $('.loadmore').hide();
                }
            }
        });

    }

</script>
</body>
</html>
