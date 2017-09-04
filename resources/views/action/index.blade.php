<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.list_header', ['addBtn' => true])
    </div>
    <div class="box-body">
        <table id="data-table" style="width: 100%" class="display nowrap table table-striped table-bordered table-hover table-condensed">
            <thead>
            <tr>
                <th>#</th>
                <th>名称</th>
                <th>方法</th>
                <th>路由</th>
                <th>控制器</th>
                <th>view路径</th>
                <th>js路径</th>
                <th>请求类型</th>
                <th>状态</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>