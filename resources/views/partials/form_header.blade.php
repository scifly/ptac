<span id="breadcrumb" style="color: #999; font-size: 13px;">
    <i class="fa fa-gears">&nbsp;&nbsp;{!! $breadcrumb ?? '' !!}</i>
</span>
<div class="box-tools pull-right">
    @if (isset($buttons))
        @foreach ($buttons as $button)
            @can('act', $uris[$button['id']])
                <button id="{{ $button['id'] }}" type="button" class="btn btn-box-tool">
                    <i class="{{ $button['icon'] }} {{ $button['color'] ?? 'text-blue' }}">
                        &nbsp;{{ $button['label'] }}
                    </i>
                </button>
            @endcan
        @endforeach
    @endif
    @if (!isset($disabled))
        @can('act', $uris['index'])
            <button id="record-list" type="button" class="btn btn-box-tool">
                <i class="fa fa-mail-reply text-blue"> 返回列表</i>
            </button>
        @endcan
    @endif
</div>