<div class="box box-primary">
    <div class="box-header">
        <div class="btn-group">
            <a href="{{ url('school_types/create') }}" class="btn btn-primary pull-right">
                <i class="fa fa-plus"></i>
                添加新学校类型
            </a>
        </div>
        <span><i class="fa fa-plus"></i></span>
    </div>
    <div class="box-body">
        <table id="data-table" class="dataTable table table-striped table-hover table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>名称</th>
                <th>备注</th>
                <th>创建时间</th>
                <th>更新时间</th>
                <th>状态</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>