<span id="breadcrumb" style="color: #999; font-size: 13px;">{!! $breadcrumb !!}</span>
<div class="box-tools pull-right">
    @if (isset($uris['create']))
        @can('act', $uris['create'])
            <button id="add-record" type="button" class="btn btn-box-tool">
                <i class="fa fa-plus text-blue"> 新增</i>
            </button>
        @endcan
    @endif
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
    @if (isset($batch))
        <div class="btn-group">
            @if (
                (isset($uris['update']) && Auth::user()->can('act', $uris['update'])) ||
                (isset($uris['destroy']) && Auth::user()->can('act', $uris['destroy']))
            )
                <button id="select-all" type="button" class="btn btn-default" title="全选">
                    <i class="fa fa-check-circle text-blue"></i>
                </button>
                <button id="deselect-all" type="button" class="btn btn-default" title="取消全选">
                    <i class="fa fa-check-circle text-gray"></i>
                </button>
            @endif
            @if (isset($uris['update']))
                @can ('act', $uris['update'])
                    <button id="batch-enable" type="button" class="btn btn-default" title="批量启用">
                        <i class="fa fa-circle text-green"></i>
                    </button>
                    <button id="batch-disable" type="button" class="btn btn-default" title="批量禁用">
                        <i class="fa fa-circle text-gray"></i>
                    </button>
                @endcan
            @endif
            @if (isset($uris['destroy']))
                @can ('act', $uris['destroy'])
                    <button id="batch-delete" type="button" class="btn btn-default" title="批量删除">
                        <i class="fa fa-trash text-red"></i>
                    </button>
                @endcan
            @endif
        </div>
    @endif
</div>