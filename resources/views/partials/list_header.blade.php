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
                    <i class="{{ $button['icon'] }} text-blue"> {{ $button['label'] }}</i>
                </button>
            @endcan
        @endforeach
    @endif
    @if (isset($batch))
        @can ('act', $uris['batch'])
            <div class="btn-group">
                <button id="select_all" type="button" class="btn btn-default">
                    <i class="fa fa-check-circle" title="全选"></i>
                </button>
                <button id="deselect_all" type="button" class="btn btn-default">
                    <i class="fa fa-check-circle-o" title="取消全选"></i>
                </button>
                <button id="enable" type="button" class="btn btn-default">
                    <i class="fa fa-circle" title="批量启用"></i>
                </button>
                <button id="activate" type="button" class="btn btn-default">
                    <i class="fa fa-circle-o" title="批量禁用"></i>
                </button>
                <button id="remove" type="button" class="btn btn-default">
                    <i class="fa fa-remove" title="批量删除"></i>
                </button>
            </div>
        @endcan
    @endif
</div>