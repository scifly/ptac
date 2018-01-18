<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta name="csrf_token" content="{{ csrf_token() }}" id="csrf_token">
    <title>WeUI</title>
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
        .main{
            height: 100%;
            width: 100%;
            background-color: #fff;
        }
        .multi-role {
            background: #fff;
            position: relative;
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

    </style>
    <head>
<body ontouchstart>

<div class="multi-role">
    <div class="switchclass-item clearfix">
        <div class="switchclass-head">

            <div class="weui-cell">
                <div class="weui-cell__bd title-name">
                    <input style="text-align: center;" id="classlist" class="weui-input" type="text" value="一年级1班" readonly="" data-values="一年级1班">
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
    <div class="weui-cells" style="margin-top: 0;">
        <a class="weui-cell weui-cell_access" href="count.html">
            <div class="weui-cell__bd">
                <p>cell standard</p>
            </div>
            <div class="weui-cell__ft">说明文字</div>
        </a>
        <a class="weui-cell weui-cell_access" href="count.html">
            <div class="weui-cell__bd">
                <p>cell standard</p>
            </div>
            <div class="weui-cell__ft">说明文字</div>
        </a>
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
    //班级列表
    $("#classlist").select({
        title: "选择班级",
        items: ["一年级1班", "一年级2班", "一年级3班", "一年级4班", "一年级5班", "一年级6班"]
    });



</script>
</body>
</html>
