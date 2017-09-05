var crud = {
    unbindEvents: function() {
        $('#add-record').unbind('click');
        $(document).off('click', '.fa-edit');
        $(document).off('click', '.fa-eye');
        $('#confirm-delete').unbind('click');
    },
    initDatatable: function(table) {
        $('#data-table').dataTable({
            processing: true,
            serverSide: true,
            ajax: page.siteRoot() + table + '/index',
            order: [[0, 'desc']],
            stateSave: true,
            autoWidth: true,
            scrollX: true,
            language: {url: '../files/ch.json'},
            lengthMenu: [[15, 25, 50, -1], [15, 25, 50, 'All']]
        });
    },
    ajaxRequest: function(requestType, ajaxUrl, data, obj) {
        $.ajax({
            type: requestType,
            dataType: 'json',
            url: ajaxUrl,
            data: data,
            success: function(result) {
                if (result.statusCode === 200) {
                    switch(requestType) {
                        case 'POST':
                            obj.reset();
                            $('input[data-render="switchery"]').each(function() {
                                // it seems dblClick() won't do the trick
                                // so just click twice
                                $(this).click(); $(this).click();
                            });
                            break;
                        case 'DELETE':
                            $('#data-table').dataTable().fnDestroy();
                            crud.initDatatable(obj);
                            break;
                        default: break;
                    }
                }
                page.inform(
                    '操作结果', result.message,
                    result.statusCode === 200 ? page.success : page.failure
                );
                return false;
            },
            error: function(e) {
                var obj = JSON.parse(e.responseText);
                page.inform('出现异常', obj['message'], page.failure);
            }
        });
    },
    init: function(homeUrl, formId, ajaxUrl, requestType) {
        // Select2
        $('select').select2();

        // Switchery
        Switcher.init();

        // iCheck
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });

        // Cancel button
        $('#cancel, #record-list').on('click', function() {
            var $activeTabPane = $('#tab_' + page.getActiveTabId());
            page.getTabContent($activeTabPane, page.siteRoot() + homeUrl);
            crud.unbindEvents();
        });

        // Parsley
        var $form = $('#' + formId);
        $form.parsley().on("form:validated", function () {
            if ($('.parsley-error').length === 0) {
                crud.ajaxRequest(requestType, page.siteRoot() + ajaxUrl, $form.serialize(), $form[0]);
            }
        }).on('form:submit', function() {
            return false;
        });
    },
    index: function (table) {
        crud.unbindEvents();
        var $activeTabPane = $('#tab_' + page.getActiveTabId());

        // 显示记录列表
        crud.initDatatable(table);

        // 新增记录
        $('#add-record').on('click', function() {
            page.getTabContent($activeTabPane, page.siteRoot() + table + '/create');
            crud.unbindEvents();
        });

        // 编辑记录
        $(document).on('click', '.fa-edit', function() {
            var url = $(this).parents().eq(0).attr('id');
            console.log(url);
            url = url.replace('_', '/');
            page.getTabContent($activeTabPane, page.siteRoot() + table + '/' + url);
            crud.unbindEvents();
        });

        // 查看记录详情
        $(document).on('click', '.fa-eye', function() {
            var url = $(this).parents().eq(0).attr('id');
            url = url.replace('_', '/');
            crud.unbindEvents();
        });

        // 删除记录
        var id/*, $row*/;
        $(document).on('click', '.fa-trash', function() {
            id = $(this).parents().eq(0).attr('id');
            // $row = $(this).parents().eq(2);
            $('#modal-dialog').modal({backdrop: true});
        });
        $('#confirm-delete').on('click', function() {
            crud.ajaxRequest(
                'DELETE', page.siteRoot() + '/' + table + '/delete/' + id,
                { _token: $('#csrf_token').attr('content') }, table
            );
        });
    },
    create: function(formId, table) {
        this.init(table + '/index', formId, table + '/store', 'POST');
    },
    edit: function (formId, table) {
        var id = $('#id').val();
        this.init(table + '/index', formId, table + '/update/' + id, 'PUT');
    }
};
