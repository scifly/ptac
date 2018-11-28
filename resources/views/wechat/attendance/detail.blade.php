@extends('layouts.wap')
@section('title') 考勤中心 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/attendance/detail.css') !!}">
@endsection
@section('content')
    <div class="multi-role">
        <div class="switchschool-item clearfix">
            <div class="switchschool-head">
                <div class="title-name"> {!! $schoolname !!}</div>
            </div>
        </div>
        <div class="kaoqin-history-calender">
            <div id="inline-calendar"></div>
            {!! Form::hidden('id', $id, ['id' => 'id']) !!}
            <table class="kaoqin-tongji js-kaoqin-tongji">
                <tbody>
                <tr>
                    <td>
                        <div class="kaoqin-date-circle okstatus"></div>
                        <span class="pl10">正常: </span>
                        <span>{!! $data['nSum'] !!} 天</span>
                    </td>
                    <td>
                        <div class="kaoqin-date-circle notstatus"></div>
                        <span class="pl10">异常: </span>
                        <span>{!! $data['aSum'] !!} 天</span>
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
                {!! $date !!}
            </div>
            @foreach($ins as $in)
                <div class="mt20 history-list-con" style="">
                    @if (sizeof($ins) != 0)
                        <span class="js-kaoqin-status-morning"
                              style="display:inline-block">{!! $in->studentAttendancesetting->name !!}
                        </span>
                        <span class="kaoqin-detail-status c-83db74">{!! $in->status ? '正常' : '异常' !!}</span>
                        <span class="time">{!! date('H:i:s', strtotime($in->punch_time)) !!}</span>
                    @endif
                </div>
            @endforeach
            @foreach ($outs as $out)
                <div class="mt20 history-list-con" style="">
                    @if (sizeof($outs) != 0)
                        <span class="js-kaoqin-status-morning" style="display:inline-block">放学</span>
                        <span class="kaoqin-detail-status c-83db74">{!! $out->status ? '正常' : '异常' !!}</span>
                        <span class="time">{!! date('H:i:s', strtotime($out->punch_time)) !!}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
@section('script')
    <script src="{!! asset('/js/wechat/attendance/detail.js') !!}"></script>
@endsection