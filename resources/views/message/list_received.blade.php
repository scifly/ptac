<table id="data-table-r" style="width: 100%"
       class="display nowrap table table-striped table-bordered table-hover table-condensed">
    <thead>
    <tr class="bg-info">
        @foreach ($rTitles as $title)
            <th>{!! !is_array($title) ? $title : $title['title'] !!}</th>
        @endforeach
    </tr>
    </thead>
    <tbody></tbody>
    @if (isset($filter))
        <tfoot style="display: table-header-group">
        <tr>
            @foreach ($rTitles as $title)
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