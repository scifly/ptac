var formUrl = '', currentJsTree, requestType;
var csrfToken = $('#csrf_token').attr('content');
var breadcrumb = '';

var nodeid; // id of the node to be deleted
// var $boxBody = $('.box-body'); // the div that holds the form
var $dialog = $('#modal-dialog');
var $menuTree = $('#jstree-menu');
var $formContainer = $('#form_container');
var $confirmDelete = $('#confirm-delete');

// URLs used on the page
var urlRoot = page.siteRoot();
var urlIndex = urlRoot + 'menus/index';
var urlSort = urlRoot + 'menus/sort';
var urlCreate = urlRoot + 'menus/create';
var urlStore = urlRoot + 'menus/store';
var urlEdit = urlRoot + 'menus/edit/';
var urlUpdate = urlRoot + 'menus/update/';
var urlMove = urlRoot + 'menus/move/';
var urlDelete = urlRoot + 'menus/delete/';
var urlRankTabs = urlRoot + 'menus/ranktabs/';
var urlMenuTabs = urlRoot + 'menus/menutabs/';
var saveRank =
    '<div class="box-tools pull-right">' +
    '    <button id="save-rank" type="button" class="btn btn-box-tool">' +
    '        <i class="fa fa-save text-blue"> 保存排序</i>' +
    '    </button>&nbsp;' +
    '    <button id="record-list" type="button" class="btn btn-box-tool">' +
    '        <i class="fa fa-mail-reply text-blue"> 返回列表</i>' +
    '   </button>' +
    '</div>';
// helper functions for menu management
var menu = {
    sort: function() {
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
            url: urlSort,
            data: { data: positions, _token: csrfToken }
        });
    },
    showTree: function() {
        $formContainer.hide();
        $menuTree.show();
        return $.ajax($menuTree.jstree().refresh());
    },
    showForm: function() {
        $menuTree.hide();
        $formContainer.show();
    },
    getForm: function(id, url, action) {
        var $form, $save, $cancel;

        menu.showForm();
        $formContainer.html(page.ajaxLoader());
        return $.ajax({
            type: 'GET',
            dataType: 'json',
            url: url,
            data: { tabId: page.getActiveTabId() },
            success: function(result) {
                var $breadcrumb = $('#breadcrumb');
                $formContainer.html(result.html);
                $breadcrumb.html(result['breadcrumb']);
                if (action === 'rank') {
                    var $tabList = $('.todo-list');
                    $tabList.sortable({
                        placeholder         : 'sort-highlight',
                        handle              : '.handle',
                        forcePlaceholderSize: true,
                        zIndex              : 999999
                    }).todoList();
                    $breadcrumb.after(saveRank);
                    $(document).on('click', '#save-rank', function() {
                        var $tabs = $('.text');
                        var ranks = {};
                        for (var i = 0; i < $tabs.length; i++) {
                            ranks[$tabs[i].id] = i;
                        }
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: formUrl,
                            data: { data: ranks, _token: csrfToken },
                            success: function(result) {
                                page.inform(
                                    '操作结果', result.message,
                                    result.statusCode === 200 ? page.success : page.failure
                                );
                            }
                        });
                    });
                    $(document).on('click', '#record-list', function() {
                        $('#breadcrumb').html(breadcrumb);
                        $('.box-tools').remove();
                        menu.showTree();
                    });
                } else {
                    $save = $('#save');
                    $cancel = $('#cancel');
                    if (action === 'create') { $('#parent_id').val(id); }
                    $('select').select2();
                    Switcher.init();
                    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
                        checkboxClass: 'icheckbox_minimal-blue',
                        radioClass: 'iradio_minimal-blue'
                    });
                    $form = $('#formMenu');
                    $form.parsley().on('form:validated', function() {
                        if ($('.parsley-error').length === 0) {
                            $.ajax({
                                type: requestType,
                                dataType: 'json',
                                url: formUrl,
                                data: $form.serialize(),
                                success: function(result) {
                                    page.inform(
                                        '操作结果', result.message,
                                        result.statusCode === 200 ? page.success : page.failure
                                    );
                                    menu.showTree();
                                    setTimeout(function() {menu.sort()}, 5000);
                                }
                            });
                        }
                    }).on('form:submit', function() { return false; });
                    $save.on('click', function() { $form.trigger('form:validate'); });
                    $cancel.on('click', function() { menu.showTree(); });
                }
            }
        });
    },
    getSelector: function(node) {
        return $.jstree.reference(node.reference).get_node(node.reference);
    }
};
$(function() {
    currentJsTree = $menuTree.jstree({
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
                url: urlIndex,
                type: 'POST',
                dataType: 'json',
                data: function(node) {
                    return { id: node.id, _token: csrfToken };
                }
            }
        },
        plugins: ['contextmenu', 'dnd', 'wholerow'],
        contextmenu: { items: customMenu }
    }).on('loaded.jstree', function() {
        $menuTree.jstree('open_all');
        menu.sort();
    }).on('move_node.jstree', function(e, data){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: urlMove + data.node.id + '/' + data.node.parent,
            data: { _token: csrfToken },
            success: function() { menu.sort(); }
        });
    });

    $confirmDelete.on('click', function() {
        $.ajax({
            type: 'DELETE',
            dataType: 'json',
            url: urlDelete + nodeid,
            data: { _token: csrfToken },
            success: function(result) {
                page.inform(
                    '操作结果', result.message,
                    result.statusCode === 200 ? page.success : page.failure
                );
                $.when(menu.sort()).done($menuTree.jstree().refresh());
            }
        });
    });
});

function customMenu() {

    return {
        createItem: {
            label: '创建',
            action: function(node) {
                formUrl = urlStore;
                requestType = 'POST';
                menu.getForm(menu.getSelector(node).id, urlCreate, 'create');
            }
        },
        renameItem: {
            label: '修改',
            action: function(node) {
                var selector = menu.getSelector(node);

                formUrl = urlUpdate + selector.id;
                requestType = 'PUT';
                menu.getForm(selector.id, urlEdit + selector.id, 'edit');
            }
        },
        deleteItem: {
            label: '删除',
            action: function(node) {
                $dialog.modal({ backdrop: true });
                nodeid = menu.getSelector(node).id;
            }
        },
        rankTabs: {
            label: '卡片排序',
            action: function(node) {
                var selector = menu.getSelector(node);

                breadcrumb = $('#breadcrumb').html();
                formUrl = urlRankTabs + selector.id;
                requestType = 'POST';
                menu.getForm(selector.id, urlMenuTabs + selector.id, 'rank');
            }
        }
    };

}