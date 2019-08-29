<span id="breadcrumb" style="color: #999; font-size: 13px;">{!! $breadcrumb !!}</span>
<div class="box-tools pull-right">
    @if (isset($uris['create']))
        @can('act', $uris['create'])
            {!! Form::button(
                Html::tag('i', ' 新增', ['class' => 'fa fa-plus text-blue']),
                ['id' => 'add-record', 'class' => 'btn btn-box-tool']
            ) !!}
        @endcan
    @endif
    @if (isset($buttons))
        @foreach ($buttons as $button)
            @can('act', $uris[$button['id']])
                {!! Form::button(
                    Html::tag('i', '&nbsp;' . $button['label'], [
                        'class' => $button['icon'] . '&nbsp;' . ($button['color'] ?? 'text-blue')
                    ]), ['id' => $button['id'], 'class' => 'btn btn-box-tool']
                ) !!}
            @endcan
        @endforeach
    @endif
    @if (isset($batch))
        <div class="btn-group">
            @if (
                (isset($uris['update']) && Auth::user()->{'can'}('act', $uris['update'])) ||
                (isset($uris['destroy']) && Auth::user()->{'can'}('act', $uris['destroy']))
            )
                {!! Form::button(
                    Html::tag('i', '', ['class' => 'fa fa-check-circle text-blue']),
                    ['id' => 'select-all', 'class' => 'btn btn-default', 'title' => '全选']
                ) !!}
                {!! Form::button(
                    Html::tag('i', '', ['class' => 'fa fa-check-circle text-gray']),
                    ['id' => 'deselect-all', 'class' => 'btn btn-default', 'title' => '取消全选']
                ) !!}
            @endif
            @if (isset($uris['update']))
                @can ('act', $uris['update'])
                    {!! Form::button(
                        Html::tag('i', '', ['class' => 'fa fa-circle text-green']),
                        ['id' => 'batch-enable', 'class' => 'btn btn-default', 'title' => '批量启用']
                    ) !!}
                    {!! Form::button(
                        Html::tag('i', '', ['class' => 'fa fa-circle text-gray']),
                        ['id' => 'batch-disable', 'class' => 'btn btn-default', 'title' => '批量禁用']
                    ) !!}
                @endcan
            @endif
            @if (isset($uris['destroy']))
                @can ('act', $uris['destroy'])
                    {!! Form::button(
                        Html::tag('i', '', ['class' => 'fa fa-trash text-red']),
                        ['id' => 'batch-delete', 'class' => 'btn btn-default', 'title' => '批量删除']
                    ) !!}
                @endcan
            @endif
        </div>
    @endif
</div>