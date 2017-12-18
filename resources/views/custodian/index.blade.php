<div class="box box-default box-solid">
    <div class="box-header with-border">
        {{ Form::hidden('export-type', 'custodian') }}
        @include('partials.list_header', [
            'buttons' => [
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
                <th>监护人姓名</th>
                <th>性别</th>
                <th>电子邮箱</th>
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
