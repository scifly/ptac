<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta name="csrf_token" content="{{ csrf_token() }}" id="csrf_token">
    <title>考勤中心</title>
    <link rel="stylesheet" href="{{ URL::asset('css/weui.min.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/jquery-weui.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/wechat/icon/iconfont.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/wechat/attendance/educator.css') }}">
</head>
<body ontouchstart>
<div class="multi-role">
    <div class="switchclass-item clearfix">
        <div class="switchclass-head">
            <div class="weui-cell">
                <div class="weui-cell__bd title-name">
                    <div style="text-align: center;">打卡详情</div>
                    <span class="icons-choose choose-icon js-choose-icon">
                        <a class="icon iconfont icon-add c-green open-popup"
                           href="javascript:" data-target="#choose">
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div id="main" style="width: 100%; height: 300px;"></div>
    <table class="kaoqin-tongji">
        <tr>
            <td>
                <a href="javascript:" class="open-popup" data-target="#studentlist" data-type="normal">
                    <div class="kaoqin-date-circle okstatus"></div>
                    <span class="pl10">正常:</span>
                    <span class="status-value">14</span>
                </a>
            </td>
            <td>
                <a href="javascript:" class="open-popup" data-target="#studentlist" data-type="abnormal">
                    <div class="kaoqin-date-circle notstatus"></div>
                    <span class="pl10">异常:</span>
                    <span class="status-value">0</span>
                </a>
            </td>
            <td>
                <a href="javascript:" class="open-popup" data-target="#studentlist" data-type="norecords">
                    <div class="kaoqin-date-circle reststatus"></div>
                    <span class="pl10">未打卡:</span>
                    <span class="status-value">0</span>
                </a>
            </td>
        </tr>
    </table>
</div>
<div id="studentlist" class="weui-popup__container">
    <div class="weui-popup__overlay"></div>
    <div class="weui-popup__modal">
        <div class="toolbar">
            <div class="toolbar-inner">
                <a href="javascript:" class="picker-button close-popup">关闭</a>
                <h1 class="title">学生列表</h1>
            </div>
        </div>
        <div class="modal-content">
            {{--<div class="list">--}}
            {{--<div class="list-item">--}}
            {{--<div class="list-item-info">--}}
            {{--<div class="username">姓名 : <span>张三</span></div>--}}
            {{--<div class="parent">监护人 : <span>张三他爸</span></div>--}}
            {{--<div class="mobile">手机 : <span>13111111111</span></div>--}}
            {{--<div class="otherinfo">其他信息（打卡时间、请假理由等）</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
        </div>
    </div>
</div>
<div id="choose" class='weui-popup__container'>
    <div class="weui-popup__overlay"></div>
    <div class="weui-popup__modal">
        <div class="weui-cells weui-cells_form">
            <div class="weui-cell">
                <div class="weui-cell__hd">
                    <label for="squad" class="weui-label">选择班级</label>
                </div>
                <div class="weui-cell__bd">
                    <input style="text-align: center;"
                           id="squad" name="squad"
                           class="weui-input"
                           type="text" value=""
                           readonly="" data-values=""
                    >
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd">
                    <label for="rule" class="weui-label">选择规则</label>
                </div>
                <div class="weui-cell__bd">
                    <input style="text-align: center;"
                           id="rule" name="rule"
                           class="weui-input"
                           type="text" value=""
                           readonly="" data-values=""
                    >
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd">
                    <label for="start-date" class="weui-label">开始日期</label>
                </div>
                <div class="weui-cell__bd">
                    <input style="text-align: center;"
                           class="weui-input"
                           id="start-date" name="time"
                           type="text" readonly=""
                    >
                </div>
            </div>
            <div class="choose-footer js-choose-footer" style="position: fixed; bottom: 0; width: 100%;">
                <a href="javascript:" class="weui-btn weui-btn_primary close-popup">确定</a>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('/js/jquery.min.js') }}"></script>
<script src="{{ asset('/js/fastclick.js') }}"></script>
<script src="{{ asset('/js/jquery-weui.min.js') }}"></script>
<script src="{{ asset('/js/plugins/echarts/echarts.common.min.js') }}"></script>
<script src="{{ asset('/js/wechat/attendance/educator.js') }}"></script>
<script>
    $(function () {
        FastClick.attach(document.body);
    });
</script>
</body>
</html>
