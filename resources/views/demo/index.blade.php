@extends('layouts.demo')
@section('title')
    <title>智慧校园</title>
@endsection
@section('content')
    <tr class="">
        <td class="" data-href="safe">
            <div>
                <div data-img="safe_school" class="eucp-jiugong-icon icon"></div>
                <div class="tit">平安校园</div>
            </div>
        </td>
        <td class="" data-href="wisdom">
            <div>
                <div data-img="wisdom_school" class="eucp-jiugong-icon icon"></div>
                <div class="tit">智慧校园</div>
            </div>
        </td>
        <td data-href="classroom">
            <div>
                <div data-img="wisdom_class" class="eucp-jiugong-icon icon"></div>
                <div class="tit">智慧课堂</div>
            </div>
        </td>
    </tr>
    <tr>
        <td data-href="info">
            <div>
                <div data-img="message" class="eucp-jiugong-icon icon"></div>
                <div class="tit">信息管理</div>
            </div>
        </td>
    </tr>
@endsection