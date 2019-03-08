<div class="box box-default box-solid">
    <div class="box-header with-border">
        <span id="breadcrumb" style="color: #999; font-size: 13px;">{!! $breadcrumb !!}</span>
        <div class="box-tools pull-right">
            @if (isset($btns))
                @foreach ($btns as $key => $btn)
                    @can('act', $uris[$key])
                        <button id="{!! $btn['id'] !!}" type="button" class="btn btn-box-tool" title="{!! $btn['title'] !!}">
                            <i class="{!! $btn['icon'] !!} {!! $btn['color'] ?? 'text-blue' !!}">
                                &nbsp;{!! $btn['label'] !!}
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