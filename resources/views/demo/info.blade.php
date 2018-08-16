@extends('layouts.demo')
@section('title')
    <title>信息管理</title>
@endsection
@section('content')
    <tr class="">
        <td class="" data-href="">
            <div>
                <div data-img="chengji" class="eucp-jiugong-icon icon"></div>
                <div class="tit">成绩推送</div>
            </div>
        </td>
        <td class="" data-href="">
            <div>
                <div data-img="kaoqin" class="eucp-jiugong-icon icon"></div>
                <div class="tit">考勤信息推送</div>
            </div>
        </td>
        <td data-href="">
            <div>
                <div data-img="xiaofei" class="eucp-jiugong-icon icon"></div>
                <div class="tit">消费通知</div>
            </div>
        </td>
    </tr>
    <tr>
        <td data-href="">
            <div>
                <div data-img="email" class="eucp-jiugong-icon icon"></div>
                <div class="tit">其他通知</div>
            </div>
        </td>
    </tr>
@endsection