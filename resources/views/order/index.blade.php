<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.list_header')
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
			<tr class="bg-info">
                <th>#</th>
                <th>姓名</th>
                <th>订单号</th>
                <th>所属套餐</th>
                <th>支付类型</th>
                <th>交易ID</th>
                <th>创建于</th>
                <th>更新于</th>
                <th>状态</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    @include('shared.form_overlay')
</div>
