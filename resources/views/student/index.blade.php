<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.list_header')
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%"
               class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
            <tr>
                <th>#</th>
                <th>学生姓名</th>
                <th>性别</th>
                <th>班级名称</th>
                <th>学号</th>
                <th>卡号</th>
                <th>是否住校</th>
                <th>电话号码</th>
                <th>生日</th>
                <th>备注</th>
                <th>创建时间</th>
                <th>更新时间</th>
                <th>状态</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    @include('partials.form_overlay')
</div>
