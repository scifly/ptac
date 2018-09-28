@extends('layouts.wap')
@section('title')
    <title>考勤中心</title>
@endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/attendance/custodian.css') !!}">
@endsection
@section('content')
    <div class="main">
        <div class="list">
            @foreach ($students as $student)
                <div class="list-item">
                    <div class="list-item-info">
                        <div class="username">姓名 : <span>{!! $student->studentname !!}</span></div>
                        <div class="school">学校 : <span>{!! $student->schoolname !!}</span></div>
                        <div class="grade">班级 : <span>{!! $student->classname !!}</span></div>
                    </div>
                    <div class="line"></div>
                    <table class="kaoqin-tongji">
                        <tr>
                            <td>
                                <div class="kaoqin-date-circle okstatus"></div>
                                <span class="pl10">正常:</span>
                                <span>{!! $student->normal ? $student->normal : '-' !!} 天</span>
                            </td>
                            <td>
                                <div class="kaoqin-date-circle notstatus"></div>
                                <span class="pl10">异常:</span>
                                <span>{!! $student->abnormal ? $student->abnormal : '-' !!} 天</span>
                            </td>
                            <td>
                                <div class="kaoqin-date-circle reststatus"></div>
                                <span class="pl10">请假:</span>
                                <span>- 天</span>
                            </td>
                        </tr>
                    </table>
                    <div class="list-item-icon">
                        <a href="{!! url(session('acronym') . '/attendances/detail/' . $student->id) !!}">
                            <i class="icon iconfont icon-jiantouyou"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection