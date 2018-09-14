<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header')
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
            <tr class="bg-info">
                @foreach ($titles as $title)
                    <th>{!! $title !!}</th>
                @endforeach
            </tr>
            </thead>
            <tbody></tbody>
            @if (isset($filter))
                <tfoot style="display: table-header-group">
                <tr>
                    @foreach ($titles as $title)
                        <th>
                            {!! Form::text('', null, [
                                'class' => 'form-control input-sm',
                                'title' => '按"' . $title . '"过滤'
                            ]) !!}
                        </th>
                    @endforeach
                </tr>
                </tfoot>
            @endif
        </table>
    </div>
    @include('partials.form_overlay')
</div>