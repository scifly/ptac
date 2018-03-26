<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header', [
            'buttons' => [
                'stat' => [
                    'id' => 'stat',
                    'label' => '统计',
                    'icon' => 'fa fa-bar-chart'
                ],
                'export' => [
                    'id' => 'export',
                    'label' => '批量导出',
                    'icon' => 'fa fa-arrow-circle-down'
                ]
            ]
        ])
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
            <tr class="bg-info">
                <th>#</th>
                <th>学生</th>
                <th>消费地点</th>
                <th>消费机ID</th>
                <th>类型</th>
                <th>金额</th>
                <th>时间</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    @include('partials.form_overlay')
</div>