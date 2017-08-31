var formUrl = '', currentJsTree, requestType;
var csrfToken = $('#csrf_token').attr('content');

var nodeid; // id of the node to be deleted
// var $boxBody = $('.box-body'); // the div that holds the form
var $dialog = $('#modal-dialog');
var $departmentTree = $('#jstree-department');
var $formContainer = $('#form_container');
var $confirmDelete = $('#confirm-delete');

// URLs used on the page
var urlRoot = page.siteRoot();
var urlIndex = urlRoot + 'departments/index';
var urlSort = urlRoot + 'departments/sort';
var urlCreate = urlRoot + 'departments/create';
var urlStore = urlRoot + 'departments/store';
var urlEdit = urlRoot + 'departments/edit/';
var urlUpdate = urlRoot + 'departments/update/';
var urlMove = urlRoot + 'departments/move/';
var urlDelete = urlRoot + 'departments/delete/';
var backToList =
    '<div class="box-tools pull-right">' +
    '    <button id="record-list" type="button" class="btn btn-box-tool">' +
    '        <i class="fa fa-mail-reply text-blue"> 返回列表</i>' +
    '   </button>' +
    '</div>';
// helper functions for department management
var department = {
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
        $departmentTree.show();
        return $.ajax($departmentTree.jstree().refresh());
    },
    showForm: function() {
        $departmentTree.hide();
        $formContainer.show();
    },
    getForm: function(id, url, action) {
        var $form, $save, $cancel;

        department.showForm();
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
                $breadcrumb.after(backToList);

                $save = $('#save');
                $cancel = $('#cancel');
                if (action === 'create') { $('#parent_id').val(id); }
                $('select').select2();
                Switcher.init();
                $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-blue',
                    radioClass: 'iradio_minimal-blue'
                });
                $form = $('#formDepartment');
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
                                department.showTree();
                                setTimeout(function() {department.sort()}, 5000);
                            }
                        });
                    }
                }).on('form:submit', function() { return false; });
                $save.on('click', function() { $form.trigger('form:validate'); });
                $('#cancel, #record-list').on('click', function() { department.showTree(); });
            }
        });
    },
    getSelector: function(node) {
        return $.jstree.reference(node.reference).get_node(node.reference);
    }
};
$(function() {
    currentJsTree = $departmentTree.jstree({
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
        $departmentTree.jstree('open_all');
        department.sort();
    }).on('move_node.jstree', function(e, data){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: urlMove + data.node.id + '/' + data.node.parent,
            data: { _token: csrfToken },
            success: function() { department.sort(); }
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
                $.when(department.sort()).done($departmentTree.jstree().refresh());
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
                department.getForm(department.getSelector(node).id, urlCreate, 'create');
            }
        },
        renameItem: {
            label: '修改',
            action: function(node) {
                var selector = department.getSelector(node);

                formUrl = urlUpdate + selector.id;
                requestType = 'PUT';
                department.getForm(selector.id, urlEdit + selector.id, 'edit');
            }
        },
        deleteItem: {
            label: '删除',
            action: function(node) {
                $dialog.modal({ backdrop: true });
                nodeid = department.getSelector(node).id;
            }
        }
    };

}