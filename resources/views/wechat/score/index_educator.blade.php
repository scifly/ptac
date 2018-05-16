<div class="multi-role">
    <div class="switchclass-item clearfix">
        <div class="switchclass-head">
            <div class="weui-cell">
                <div class="weui-cell__bd title-name">
                    <input style="text-align: center;" id="classlist" class="weui-input" type="text" title=""
                           value="@if(!empty($scores)) {{$scores[0]['classname']}} @endif"
                           readonly="" data-values="@if(!empty($scores)){{$scores[0]['class_id']}} @endif">
                </div>
            </div>
        </div>
    </div>
    <div class="weui-search-bar" id="searchBar">
        <form class="weui-search-bar__form" action="#">
            <div class="weui-search-bar__box">
                <i class="weui-icon-search"></i>
                <input type="search" class="weui-search-bar__input" id="searchInput" placeholder="搜索" required="">
                <a href="#" class="weui-icon-clear" id="searchClear"></a>
            </div>
            <label class="weui-search-bar__label" id="searchText" style="transform-origin: 0 0 0; opacity: 1; transform: scale(1, 1);">
                <i class="weui-icon-search"></i>
                <span>搜索</span>
            </label>
        </form>
        <a href="#" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
    </div>
    <div class="weui-cells" style="margin-top: 0;">
        @if (!empty($scores))
            @foreach($scores as $s)
                <a class="weui-cell weui-cell_access"
                   href='{{ url("wechat/score/detail?examId=".$s['id']."&classId=".$s['class_id']) }}'>
                    <div class="weui-cell__bd"><p>{{ $s['name'] }}</p></div>
                    <div class="weui-cell__ft time">{{ $s['start_date'] }}</div>
                </a>
            @endforeach
        @else
            暂无数据
        @endif
    </div>
    <div class="loadmore">
        <span class="weui-loadmore__tips"><i class="icon iconfont icon-shuaxin"></i>加载更多</span>
    </div>
</div>
