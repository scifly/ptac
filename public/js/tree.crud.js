var nodeid;
var tree = {
    nodeTypes: {
        '#': { "icon": 'glyphicon glyphicon-flash' },
        'root': { "icon": 'fa fa-sitemap' },
        'company': { "icon": 'fa fa-building' },
        'corp': { "icon": 'fa fa-weixin' },
        'school': { "icon": 'fa fa-university' },
        'grade': { "icon": 'fa fa-users' },
        'class': { "icon": 'fa fa-user' },
        'other': { "icon": 'fa fa-list' }
    },
    csrfToken: function() { return $('#csrf_token').attr('content'); },
    urlIndex: function(table) { return table +'/index'; },
    urlCreate: function(table) { return table + '/create'; },
    urlEdit: function(table) { return table + '/edit/'; },
    urlMenuTabs: function(table) { return table + '/menutabs/'; },
    urlSort: function(table) { return page.siteRoot() + table + '/sort'; },
    urlStore: function(table) { return page.siteRoot() + table + '/store'; },
    urlUpdate: function(table) { return page.siteRoot() + table + '/update/'; },
    urlMove: function(table) { return page.siteRoot() + table + '/move/'; },
    urlDelete: function(table) { return page.siteRoot() + table + '/delete/'; },
    urlRankTabs: function(table) { return page.siteRoot() + table + '/ranktabs/'; },
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
                    icons: table !== 'menus',
                    stripes: true
                },
                expand_selected_onload: true,
                check_callback: function(o, n, p, i, m) {
                    return tree.checkCallback(o, n, p, i, m, this, table);
                },
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
            plugins: ['contextmenu', 'dnd', 'wholerow', 'unique', 'types'],
            types: tree.nodeTypes,
            contextmenu: {
                items: function(node) {
                    return tree.customMenu(
                        node, table, $('#modal-dialog'),
                        $('#tab_' + page.getActiveTabId())
                    );
                }
            }
        }).on('loaded.jstree', function() {
            $tree.jstree('open_all');
            tree.sort(table);
        }).on('move_node.jstree', function(e, data) {
            return tree.move(table, e, data);
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
        var $tree = $('#tree');
        var positions = {};
        $($tree.jstree().get_json($tree, {flat: true})).each(function(index, value) {
            // var node = $("#tree").jstree().get_node(this.id);
            // var lvl = node.parents.length;
            // console.log('node index = ' + index + ' level = ' + lvl + ' id = ' + node.id);
            positions[value.id] = index;
        });
        // console.log(positions);
        return $.ajax({
            type: 'POST',
            dataType: 'json',
            url: tree.urlSort(table),
            data: { data: positions, _token: tree.csrfToken() }
        });
    },
    move: function (table, e, data) {
        var id = data.node.id;
        var parentId = data.node.parent;
        return $.ajax({
            type: 'POST',
            dataType: 'json',
            url: tree.urlMove(table) + id + '/' + parentId,
            data: { _token: tree.csrfToken() },
            success: function(result) {
                if (result.statusCode === 200) {
                    tree.sort(table);
                } else {
                    page.inform(
                        '操作结果', result.message,
                        result.statusCode === 200 ? page.success : page.failure
                    );
                    $('#tree').jstree().refresh();
                }
            }
        });
    },
    checkCallback: function(o, n, p, i, m, t, table) {
        // o - operation, n - node, p - node_parent, i - node_position, m - more, t - this
        // m.pos: 'b' - addBefore, 'a' - addAfter, 'i' - append
        // first | last | after | before
        // if(m && m.dnd && m.pos !== 'i') { return false; }
        // if(o === "move_node" || o === "copy_node") {
        //    if(this.get_node(p).id === '#') { return false; }
        // }
        var nType = t.get_node(n).type; // 节点类型
        var pType = t.get_node(p).type; // 父节点类型
        var grandParents, grandParentTypes;
        if (o === "move_node" || o === "copy_node") {
            switch (table) {
                case 'departments':
                    switch (nType) {
                        case 'company': return pType === 'root';
                        case 'corp': return pType === 'company';
                        case 'school': return pType === 'corp';
                        case 'grade':
                            switch (pType) {
                                case 'school': return true;
                                case 'other':
                                    grandParents = t.get_node(p).parents;
                                    grandParentTypes = [];
                                    $.each(grandParents, function() {
                                        grandParentTypes.push($('#tree').jstree(true).get_node(this).type);
                                    });
                                    if ($.inArray('grade', grandParentTypes) > -1) { return false; }
                                    if ($.inArray('class', grandParentTypes) > -1) { return false; }
                                    return $.inArray('school', grandParentTypes) > -1;
                                default: return false;
                            }
                            // return $.inArray(pType, ['school', 'other']) > -1;
                            break;
                        case 'class':
                            switch (pType) {
                                case 'grade': return true;
                                case 'other':
                                    grandParents = t.get_node(p).parents;
                                    grandParentTypes = [];
                                    $.each(grandParents, function() {
                                        grandParentTypes.push($('#tree').jstree(true).get_node(this).type);
                                    });
                                    return $.inArray('grade', grandParentTypes) > -1;
                                default: return false;
                            }
                            break;
                        case 'other':
                            var children = t.get_node(n).children_d;
                            var childTypes = [];
                            $.each(children, function() {
                                var type = $('#tree').jstree(true).get_node(this).type;
                                childTypes.push(type);
                            });
                            switch (pType) {
                                case 'school':
                                    if ($.inArray('grade', childTypes) > -1) { return true; }
                                    return !($.inArray('class', childTypes) > -1) && !($.inArray('grade', childTypes) > -1);
                                case 'grade':
                                    return !($.inArray('grade', childTypes) > -1);
                                case 'class':
                                    return !($.inArray('class', childTypes) > -1) && !($.inArray('grade', childTypes) > -1);
                                case 'other':
                                    grandParents = t.get_node(p).parents;
                                    grandParentTypes = [];
                                    $.each(grandParents, function() {
                                        grandParentTypes.push($('#tree').jstree(true).get_node(this).type);
                                    });
                                    var c = childTypes, g = grandParentTypes;
                                    // neither grade nor class
                                    if (!($.inArray('class', g) > -1) && !($.inArray('grade', g) > -1)) {
                                        if (!($.inArray('class', c) > -1) && !($.inArray('grade', c) > -1)) { return true; }
                                        return $.inArray('grade', c) > -1;
                                    }
                                    // grade, no class
                                    if ($.inArray('grade', g) > -1 && !($.inArray('class', g) > -1)) {
                                        return $.inArray('grade', c) <= -1;
                                    }
                                    // class
                                    if ($.inArray('class', g) > -1) {
                                        return !($.inArray('class', c) > -1) && !($.inArray('grade', c) > -1);
                                    }
                                    break;
                                default: return false;
                            }
                            // return $.inArray(pType, ['school', 'grade', 'class', 'other']) > -1;
                            break;
                        default: return false;
                    }
                    break;
                case 'menus':
                    switch (nType) {
                        case 'company': return pType === 'root';
                        case 'corp': return pType === 'company';
                        case 'school': return pType === 'corp';
                        default: return $.inArray(pType, ['company', 'corp', 'school', 'other', 'root']) > -1;
                    }
                    break;
                default: return false;
            }
        }
        return true;
    },
    getSelector: function(node) {
        return $.jstree.reference(node.reference).get_node(node.reference);
    },
    customMenu: function(node, table, $dialog, $activeTabPane) {
        var create = {
            label: '创建',
            icon: 'fa fa-plus',
            action: function (node) {
                var url = tree.urlCreate(table) + '/' + tree.getSelector(node).id;
                page.getTabContent($activeTabPane, url);
            }
        };
        var edit = {
            label: '编辑',
            icon: 'fa fa-edit',
            action: function (node) {
                var url = tree.urlEdit(table) + tree.getSelector(node).id;
                page.getTabContent($activeTabPane, url);
            }
        };
        var del = {
            label: '删除',
            icon: 'fa fa-remove',
            action: function (node) {
                $dialog.modal({backdrop: true});
                nodeid = tree.getSelector(node).id;
            },
            _disabled: function(node) {
                var children = tree.getSelector(node).children_d;
                var type, disabled = false;
                $.each(children, function() {
                    type = $('#tree').jstree(true).get_node(this).type;
                    disabled = $.inArray(type, ['grade', 'class']) > -1;
                    if (disabled) return false;
                });
                return disabled;
            }
        };
        var rank = {
            label: '卡片排序',
            icon: 'fa fa-navicon',
            action: function (node) {
                nodeid = tree.getSelector(node).id;
                var url = tree.urlMenuTabs(table) + tree.getSelector(node).id;
                page.getTabContent($activeTabPane, url);
            }
        };
        switch (table) {
            case 'departments':
                switch (node.type) {
                    case 'school':
                    case 'grade':
                    case 'class':
                        return { createItem: create };
                    case 'other':
                        return { createItem: create, renameItem: edit, delItem: del };
                    default:
                        return { };
                }
                break;
            case 'menus':
                switch (node.type) {
                    case 'root':
                    case 'company':
                    case 'corp':
                    case 'school':
                        return { createItem: create};
                    case 'other':
                        return { createItem: create, renameItem: edit, delItem: del, rankTabs: rank };
                    default:
                        return { }
                }
                break;
            default:
                return { };
        }
    }
};