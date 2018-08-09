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
</table>