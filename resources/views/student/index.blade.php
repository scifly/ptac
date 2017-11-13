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
                <th>姓名</th>
                <th>性别</th>
                <th>班级</th>
                <th>学号</th>
                <th>卡号</th>
                <th>住校</th>
                <th>手机</th>
                <th>生日</th>
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
