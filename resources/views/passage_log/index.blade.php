<div class="box box-default box-solid">
    <div class="box-header with-border">
        <span id="breadcrumb" style="color: #999; font-size: 13px;">{!! $breadcrumb !!}</span>
        <div class="box-tools pull-right">
            @foreach ($buttons as $key => $btn)
                @can('act', $uris[$key])
                    <button id="{!! $btn['id'] !!}" type="button" class="btn btn-box-tool" title="{!! $btn['label'] !!}">
                        <i class="{!! $btn['icon'] !!} {!! $btn['color'] ?? 'text-blue' !!}">
                            &nbsp;{!! $btn['label'] !!}
                        </i>
                    </button>
                @endcan
            @endforeach
            <div class="btn-group">
                <button id="select-all" type="button" class="btn btn-default" title="全选">
                    <i class="fa fa-check-circle text-blue"></i>
                </button>
                <button id="deselect-all" type="button" class="btn btn-default" title="取消全选">
                    <i class="fa fa-check-circle text-gray"></i>
                </button>
            </div>
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
        </table>
    </div>
    @include('shared.form_overlay')
</div>