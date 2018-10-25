<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.list_header')
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