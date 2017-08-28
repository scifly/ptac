<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.list_header', ['addBtn' => true])
    </div>
    <div class="box-body">
        <table id="data-table" class="table table-striped table-bordered table-hover table-condensed">
            <thead>
            <tr>
                <th>#</th>
                <th>所属网站模块</th>
                <th>文章名称</th>
                <th>文章摘要</th>
                <th>创建时间</th>
                <th>更新时间</th>
                <th>状态</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
