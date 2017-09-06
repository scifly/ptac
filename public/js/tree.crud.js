var nodeid;
var tree = {
    csrfToken: function() { return $('#csrf_token').attr('content'); },
    urlIndex: function(table) { return page.siteRoot() + table +'/index'; },
    urlSort: function(table) { return page.siteRoot() + table + '/sort'; },
    urlCreate: function(table) { return page.siteRoot() + table + '/create'; },
    urlStore: function(table) { return page.siteRoot() + table + '/store'; },
    urlEdit: function(table) { return page.siteRoot() + table + '/edit/'; },
    urlUpdate: function(table) { return page.siteRoot() + table + '/update/'; },
    urlMove: function(table) { return page.siteRoot() + table + '/move/'; },
    urlDelete: function(table) { return page.siteRoot() + table + '/delete/'; },
    urlRankTabs: function(table) { return page.siteRoot() + table + '/ranktabs/'; },
    urlMenuTabs: function(table) { return page.siteRoot() + table + '/menutabs/'; },
    ajaxRequest: function(requestType, ajaxUrl, data, obj) {
        $.ajax({
            type: requestType,
            dataType: 'json',
            url: ajaxUrl,
            data: data,
            success: function(result) {
                if (result.statusCode === 200) {
                    switch(requestType) {
                        case 'POST':        // create
                            obj.reset();    // reset create form
                            $('input[data-render="switchery"]').each(function() {
                                $(this).click(); $(this).click();
                            });
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
    backToHome: function(table) {
        var $activeTabPane = $('#tab_' + page.getActiveTabId());
        page.getTabContent($activeTabPane, tree.urlIndex(table));
    },
    init: function(table, formId, ajaxUrl, requestType) {
        // Select2
        $('select').select2();
        // Switchery
        Switcher.init();
        // iCheck
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        // Save button
        $('#save').on('click', function() { $form.trigger('form:validate'); });
        // Cancel button
        $('#cancel, #record-list').on('click', function() { tree.backToHome(table); });
        // Parsley
        var $form = $('#' + formId);
        $form.parsley().on("form:validated", function () {
            if ($('.parsley-error').length === 0) {
                tree.ajaxRequest(requestType, ajaxUrl, $form.serialize(), $form[0]);
            }
        }).on('form:submit', function() { return false; });
        $('#confirm-delete').unbind('click');
    },
    index: function(table) {
        var $tree = $('#tree');
        $tree.jstree({
            core: {
                themes: {
                    variant: 'large',
                    dots: true,      // this setting is conflict with 'wholerow' plugin
                    icons: false,
                    stripes: true
                },
                expand_selected_onload: true,
                check_callback: true,
                multiple: false,
                animation: 0,
                data: {
                    url: tree.urlIndex(table),
                    type: 'POST',
                    dataType: 'json',
                    data: function(node) {
                        return { id: node.id, _token: tree.csrfToken() };
                    }
                }
            },
            plugins: ['contextmenu', 'dnd', 'wholerow'],
            contextmenu: { items: tree.customMenu(
                table, $('#modal-dialog'), $('#tab_' + page.getActiveTabId())
            ) }
        }).on('loaded.jstree', function() {
            $tree.jstree('open_all');
            tree.sort(table);

        }).on('move_node.jstree', function(e, data){
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: tree.urlMove(table) + data.node.id + '/' + data.node.parent,
                data: { _token: tree.csrfToken() },
                success: function() { tree.sort(table); }
            });
        });
        var $delete = $('#confirm-delete');
        $delete.on('click', function() {
            $.ajax({
                type: 'DELETE',
                dataType: 'json',
                url: tree.urlDelete(table) + nodeid,
                data: { _token: tree.csrfToken() },
                success: function(result) {
                    page.inform(
                        '操作结果', result.message,
                        result.statusCode === 200 ? page.success : page.failure
                    );
                    $.when(tree.sort(table)).done($tree.jstree().refresh());
                }
            });
            // $delete.unbind('click');
        });
    },
    create: function(formId, table) {
        this.init(table, formId, tree.urlStore(table), 'POST');
    },
    edit: function(formId, table) {
        var id = $('#id').val();
        this.init(table, formId, tree.urlUpdate(table) + id, 'PUT');
    },
    rank: function(table) {
        var $tabList = $('.todo-list');
        $tabList.sortable({
            placeholder         : 'sort-highlight',
            handle              : '.handle',
            forcePlaceholderSize: true,
            zIndex              : 999999
        }).todoList();
        $(document).on('click', '#save-rank', function() {
            var $tabs = $('.text');

            var ranks = {};
            for (var i = 0; i < $tabs.length; i++) {
                ranks[$tabs[i].id] = i;
            }
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: tree.urlRankTabs(table) + nodeid,
                data: { data: ranks, _token: tree.csrfToken() },
                success: function(result) {
                    page.inform(
                        '操作结果', result.message,
                        result.statusCode === 200 ? page.success : page.failure
                    );
                }
            });
        });
        $('#record-list').on('click', function() { tree.backToHome(table); });
        $('#confirm-delete').unbind('click');
    },
    sort: function(table) {
        // save positions of all nodes
        var $nodes = $("li[role='treeitem']");
        console.log($nodes);
        var positions = {};
        for (var i = 0; i < $nodes.length; i++) {
            positions[$nodes[i].id] = i;
        }
        return $.ajax({
            type: 'POST',
            dataType: 'json',
            url: tree.urlSort(table),
            data: { data: positions, _token: tree.csrfToken() }
        });
    },
    getSelector: function(node) {
        return $.jstree.reference(node.reference).get_node(node.reference);
    },
    customMenu: function(table, $dialog, $activeTabPane) {
        var create = {
            label: '创建',
            action: function(node) {
                page.getTabContent($activeTabPane, tree.urlCreate(table) + '/' + tree.getSelector(node).id);
            }
        };
        var edit = {
            label: '编辑',
            action: function(node) {
                page.getTabContent($activeTabPane, tree.urlEdit(table) + tree.getSelector(node).id);
            }
        };
        var del = {
            label: '删除',
            action: function(node) {
                $dialog.modal({ backdrop: true });
                nodeid = tree.getSelector(node).id;
            }
        };
        var rank = {
            label: '卡片排序',
            action: function(node) {
                nodeid = tree.getSelector(node).id;
                page.getTabContent($activeTabPane, tree.urlMenuTabs(table) + tree.getSelector(node).id)
            }
        };
        if (table === 'departments') {
            return { createItem: create, renameItem: edit, deleteItem: del };
        }
        return { createItem: create, renameItem: edit, deleteItem: del, rankTabs: rank };
    }
};