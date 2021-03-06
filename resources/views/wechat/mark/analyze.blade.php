@extends('layouts.wap')
@section('title') 成绩中心 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('score') !!}">
@endsection
@section('content')
    <header class="wechat-header">
        <h1 class="wechat-title">成绩中心</h1>
        <p class="wechat-sub-title">
            {!! $data['examName'] . ' : ' . $data['className'] !!}
        </p>
    </header>
    <div class="main" style="width: 92%;padding: 0 4%;">
        @if(!empty($data['oneData']))
            @foreach($data['oneData'] as $one)
                <div class="subjectItem" id="lie-{!! $one['subId'] !!}">
                    <div class="subj-title">{!! $one['sub'] !!}</div>
                    <div class="subj-tab">
                        <a class="tab-item cur" data-type="score">分数统计</a>
                        <a class="tab-item" data-type="score-level">分数段统计</a>
                        <a class="tab-item" data-type="table">图表统计</a>
                    </div>
                    <div class="subj-main">
                        <div class="show-item score cur">
                            <div class="table-title">{!! $one['sub'] !!}分数统计详情</div>
                            <table class="table-count">
                                <tr>
                                    <td class="subtit">统计人数</td>
                                    <td>{!! $one['count'] !!}</td>
                                </tr>
                                <tr>
                                    <td class="subtit">最高分</td>
                                    <td>{!! $one['max'] !!}</td>
                                </tr>
                                <tr>
                                    <td class="subtit">最低分</td>
                                    <td>{!! $one['min'] !!}</td>
                                </tr>
                                <tr>
                                    <td class="subtit">平均分</td>
                                    <td>{!! $one['avg'] !!}</td>
                                </tr>
                                <tr>
                                    <td class="subtit">平均分以上人数</td>
                                    <td>{!! $one['big_number'] !!}</td>
                                </tr>
                                <tr>
                                    <td class="subtit">平均分以下人数</td>
                                    <td>{!! $one['min_number'] !!}</td>
                                </tr>
                            </table>
                        </div>
                        @if(!empty($data['rangs']))
                            <div class="show-item score-level">
                                <div class="table-title">{!! $one['sub'] !!}分数统计详情</div>
                                <table class="table-count">
                                    <tr>
                                        <td class="subtit">统计人数</td>
                                        @if(!empty($data['rangs'][$one['subId']][0]['score']['count']))
                                            <td>{!! $data['rangs'][$one['subId']][0]['score']['count'] !!}</td>
                                        @else
                                            <td>0</td>
                                        @endif
                                    </tr>
                                    @if(!empty($data['rangs'][$one['subId']]))
                                        @foreach($data['rangs'][$one['subId']] as $ran)
                                            <tr>
                                                <td class="subtit">{!! $ran['range']['min'] !!}
                                                    - {!! $ran['range']['max'] !!}分
                                                </td>
                                                <td>{!! $ran['score']['number'] !!}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </table>
                            </div>
                        @endif
                        <div class="show-item table">
                            <div id="main"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div>
                <p style="text-align: center;">本场考试未录入本班数据！</p>
            </div>
        @endif
    </div>
    <div style="height: 70px;width: 100%;"></div>
    <div class="anchor-point">
        <ul>
            @if(!empty($data['oneData']))
                @foreach($data['oneData'] as $datum)
                    <li><a href="#lie-{!! $datum['subId'] !!}">{!! $datum['sub'] !!}</a></li>
                @endforeach
            @endif
        </ul>
    </div>
    <div class="footerTab">
        <a class="btnItem" href='{!! url($acronym . "/scores/detail?examId=". $examId ."&targetId=". $classId) !!}'>
            <i class="icon iconfont icon-document"></i>
            <p>详情</p>
        </a>
        <a class="btnItem footer-active">
            <i class="icon iconfont icon-renzheng7"></i>
            <p>统计</p>
        </a>
        <div style="clear: both;"></div>
    </div>
@endsection
@section('script')
    <script src="{!! asset('score') !!}"></script>
@endsection
