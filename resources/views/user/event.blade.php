<link rel="stylesheet" href="{{ URL::asset('js/plugins/datatables/datatables.min.css') }}">
<section class="content clearfix">
    @include('partials.modal_dialog')
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
            <div class="box box-default box-solid">
                <div class="box-header with-border">
                    <span id="breadcrumb" style="color: #999; font-size: 13px;">用户中心/信息列表</span>
                </div>
                <div class="box-body">
                    <table id="data-table" style="width: 100%"
                           class="display nowrap table table-striped table-bordered table-hover table-condensed">
                        <thead>
                        <tr class="bg-info">
                            <th>#</th>
                            <th>通信方式</th>
                            <th>应用</th>
                            <th>消息批次</th>
                            <th>发送者</th>
                            <th>类型</th>
                            <th>已读</th>
                            <th>已发</th>
                            <th>创建于</th>
                            <th>更新于</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                @include('partials.form_overlay')
            </div>
        </div>
    </div>
</section>
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/i18n/zh_cn.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/i18n/zh_cn.extra.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/datatables/dataTables.checkboxes.min.js') }}"></script>
<script>
    var table;
    function initDatatable() {
        table = $('#data-table').dataTable({
            processing: true,
            serverSide: true,
            ajax: 'events',
            order: [[0, 'desc']],
            stateSave: true,
            autoWidth: true,
            columnDefs: [
                { className: 'text-center', targets: [0, 1, 2, 3, 4, 5, 6,7,8] },
                // { className: 'text-right', targets: [9] }
            ],
            scrollX: true,
            language: {url: '../files/ch.json'},
            lengthMenu: [[15, 25, 50, -1], [15, 25, 50, '所有']]
        });
    }
    initDatatable();
</script>