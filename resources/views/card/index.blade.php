<div class="box box-default box-solid">
    <div class="box-header with-border">
        <span id="breadcrumb" style="color: #999; font-size: 13px;">{!! $breadcrumb !!}</span>
        <div class="box-tools pull-right">
            @if (isset($buttons))
                @foreach ($buttons as $key => $btn)
                    @can('act', $uris[$key])
                        {!! Form::button(
                            Html::tag('i', $btn['label'], [
                                'class' => join(' ', [$btn['icon'], $btn['color'] ?? 'text-blue'])
                            ]),
                            ['id' => $btn['id'], 'class' => 'btn btn-box-tool', 'title' => $btn['title']]
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
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
            <tr class="bg-info">
                @foreach ($titles as $title)
                    <th>{!! !is_array($title) ? $title : $title['title'] !!}</th>
                @endforeach
            </tr>
            </thead>
            <tbody></tbody>
            @if (isset($filter))
                <tfoot style="display: table-header-group">
                <tr>
                    @foreach ($titles as $title)
                        <th>
                            @if (!is_array($title))
                                {!! Form::text('', null, [
                                    'class' => 'form-control',
                                    'title' => '按"' . $title . '"过滤'
                                ]) !!}
                            @else
                                {!! $title['html'] !!}
                            @endif
                        </th>
                    @endforeach
                </tr>
                </tfoot>
            @endif
        </table>
    </div>
    @include('shared.form_overlay')
</div>