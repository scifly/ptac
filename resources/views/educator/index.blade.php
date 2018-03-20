<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header', [
            'buttons' => [
                'import' => [
                    'id' => 'import',
                    'label' => '批量导入',
                    'icon' => 'fa fa-arrow-circle-up'
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
                <th>姓名</th>
                <th>所属学校</th>
                <th>手机号码</th>
                <th>创建于</th>
                <th>更新于</th>
                <th>状态</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    @include('partials.form_overlay')
</div>

<!-- 导入excel -->
@include('partials.contact_import')

<!-- 导出excel -->
@include('partials.contact_range')