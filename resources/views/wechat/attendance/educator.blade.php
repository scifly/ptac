@extends('layouts.wap')
@section('title')
    <title>考勤中心</title>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/wechat/attendance/educator.css') }}">
@endsection
@section('content')
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
        <div id="main" style="width: 100%; height: 300px;">
            <span class="warning">
                暂无图表，请点击右上角【+】按钮选择班级/规则/日期查看打卡详情
            </span>
        </div>
        <table class="kaoqin-tongji">
            <tr>
                <td>
                    <a href="javascript:" class="open-popup" data-target="#studentlist" data-type="normal">
                        <div class="kaoqin-date-circle okstatus"></div>
                        <span class="pl10">正常:</span>
                        <span class="status-value">-</span>
                    </a>
                </td>
                <td>
                    <a href="javascript:" class="open-popup" data-target="#studentlist" data-type="abnormal">
                        <div class="kaoqin-date-circle notstatus"></div>
                        <span class="pl10">异常:</span>
                        <span class="status-value">-</span>
                    </a>
                </td>
                <td>
                    <a href="javascript:" class="open-popup" data-target="#studentlist" data-type="norecords">
                        <div class="kaoqin-date-circle reststatus"></div>
                        <span class="pl10">未打卡:</span>
                        <span class="status-value">-</span>
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
                <span class="warning">
                    (暂无数据)
                </span>
            </div>
        </div>
    </div>
    <div id="choose" class='weui-popup__container'>
        <div class="weui-popup__overlay"></div>
        <div class="weui-popup__modal">
            <div class="weui-cells weui-cells_form">
                {!! Form::hidden('passed', 0, ['id' => 'passed']) !!}
                <div class="weui-cell weui-cell_select weui-cell_select-after">
                    <div class="weui-cell__hd">
                        <label for="class_id" class="weui-label">班级</label>
                    </div>
                    <div class="weui-cell__bd">
                        {!! Form::select('class_id', $classes, null, [
                            'id' => 'class_id',
                            'class' => 'weui-select',
                        ]) !!}
                    </div>
                </div>
                <div class="weui-cell weui-cell_select weui-cell_select-after">
                    <div class="weui-cell__hd">
                        <label for="sas_id" class="weui-label">规则</label>
                    </div>
                    <div class="weui-cell__bd">
                        {!! Form::select('sas_id', $sases, null, [
                            'id' => 'sas_id',
                            'class' => 'weui-select',
                        ]) !!}
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd">
                        <label for="start_date" class="weui-label">日期</label>
                    </div>
                    <div class="weui-cell__bd">
                        {!! Form::text('start_date', date('Y-m-d'), [
                            'id' => 'start_date',
                            'class' => 'weui-input',
                        ]) !!}
                    </div>
                </div>
                <div class="choose-footer js-choose-footer" style="position: fixed; bottom: 0; width: 100%;">
                    <a href="javascript:" class="weui-btn weui-btn_primary close-popup">确定</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('/js/wechat/attendance/educator.js') }}"></script>
@endsection