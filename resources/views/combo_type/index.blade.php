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
                <th>套餐名称</th>
                <th>金额</th>
                <th>折扣</th>
                <th>所属学校</th>
                <th>有效月数</th>
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