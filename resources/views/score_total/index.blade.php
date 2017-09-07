<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.list_header', ['addBtn' => true])
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%" class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
            <tr>
                <th>#</th>
                <th>学号</th>
                <th>姓名</th>
                <th>考试名称</th>
                <th>总成绩</th>
                <th>班级排名</th>
                <th>年级排名</th>
                <th>创建时间</th>
                <th>更新时间</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>