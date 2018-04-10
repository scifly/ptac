<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header')
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
            <tr class="bg-info">
                <th>#</th>
                @foreach ($titles as $title)
                    <th>{!! $title !!}</th>
                @endforeach
                <th><i class="fa fa-gear"></i></th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    @include('partials.form_overlay')
</div>