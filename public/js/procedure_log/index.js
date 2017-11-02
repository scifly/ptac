$(function(){
    page.unbindEvents();

    var $activeTabPane = $('#tab_' + page.getActiveTabId());

    // 显示记录列表
    initDatatable($('#data-table-my'),'index');
    initDatatable($('#data-table-pending'),'pending');

    // 新增记录
    $('#add-record').on('click', function () {
        page.getTabContent($activeTabPane, table + '/create');
    });

    // 查看记录详情
    $(document).on('click', '.fa-eye', function () {
        var url = $(this).parents().eq(0).attr('id');
        url = url.replace('_', '/');
    });
});
function initDatatable (obj,action) {
    obj.dataTable({
        processing: true,
        serverSide: true,
        ajax: page.siteRoot() + 'procedure_logs/' + action,
        order: [[0, 'desc']],
        stateSave: true,
        autoWidth: true,
        scrollX: true,
        language: {url: '../files/ch.json'},
        lengthMenu: [[15, 25, 50, -1], [15, 25, 50, '所有']],
        dom: '<"row"<"col-md-6"l><"col-sm-4"f><"col-sm-2"B>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
        buttons: ['pdf', 'csv']
    }).on('init.dt', function () {
        $('.dt-buttons').addClass('pull-right');
        $('.buttons-pdf').addClass('btn-sm');
        $('.buttons-csv').addClass('btn-sm');
        // $('.paginate_button').each(function() { $(this).addClass('btn-sm'); })
    });
}