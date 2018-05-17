<div class="multi-role">
    <div class="header">
        <div class="switchclass-item clearfix">
            <div class="switchclass-head">
                <div class="weui-cell">
                    <div class="weui-cell__bd title-name">
                        {!! Form::select(
                            $role == '监护人' ? 'student_id' : 'class_id',
                            $role == '监护人' ? $students : $classes,
                            null, ['class' => 'weui-input']
                        ) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="weui-search-bar" id="searchBar">
            <form class="weui-search-bar__form" action="#">
                <div class="weui-search-bar__box">
                    <i class="weui-icon-search"></i>
                    {!! Form::search('search', null, [
                        'id' => 'search',
                        'class' => 'weui-search-bar__input',
                        'placeholder' => '搜索',
                        'data-type' => $role == '监护人' ? 'custodian' : 'educator'
                    ]) !!}
                    <a href="#" class="weui-icon-clear" id="searchClear"></a>
                </div>
                <label class="weui-search-bar__label" id="searchText"
                       style="transform-origin: 0 0 0; opacity: 1; transform: scale(1, 1);">
                    <i class="weui-icon-search"></i>
                    <span>搜索</span>
                </label>
            </form>
            <a href="#" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
        </div>
    </div>
    <!--考试列表-->
    <div class="weui-cells" style="margin-top: 0;">
        @if (!empty($exams))
            @foreach ($exams as $exam)
                <a class="weui-cell weui-cell_access"
                   href='{{ url("wechat/score/student_detail?examId=" . $exam['id'] . "&studentId=" . $exam['student_id']) }}'>
                    <div class="weui-cell__bd">
                        <p>{{ $exam['name'] }}</p>
                    </div>
                    <div class="weui-cell__ft time">{{ $exam['start_date'] }}</div>
                </a>
            @endforeach
        @else
            暂无数据
        @endif
    </div>
    <div class="loadmore">
        <span class="weui-loadmore__tips"><i class="icon iconfont icon-shuaxin"></i>加载更多 </span>
    </div>
</div>