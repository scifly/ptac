//# sourceURL=tree.js
(function ($) {
    $.tree = function (options) {
        var nodeid;
        var tree = {
            options: $.extend({
                departmentTypes: {
                    '#': {"icon": 'glyphicon glyphicon-flash'},
                    'root': {"icon": 'fa fa-sitemap'},
                    'company': {"icon": 'fa fa-building'},
                    'corp': {"icon": 'fa fa-weixin'},
                    'school': {"icon": 'fa fa-university'},
                    'grade': {"icon": 'fa fa-object-group'},
                    'class': {"icon": 'fa fa-users'},
                    'other': {"icon": 'fa fa-list'}
                },
                contactTypes: {
                    '#': {"icon": 'fa fa-folder'},
                    // 'dept': {"icon": 'fa fa-folder'},
                    'user': {"icon": 'fa fa-user'},
                    'root': {"icon": 'fa fa-sitemap'},
                    'company': {"icon": 'fa fa-building text-blue'},
                    'corp': {"icon": 'fa fa-weixin text-green'},
                    'school': {"icon": 'fa fa-university text-purple'},
                    'grade': {"icon": 'fa fa-object-group'},
                    'class': {"icon": 'fa fa-users'},
                    'other': {"icon": 'fa fa-folder'}
                },
            }, options),
            to: 0,

            // 菜单或部门树管理
            themes: function (displayIcon) {
                return {
                    variant: 'large',
                    dots: true,      // this setting is conflict with 'wholerow' plugin
                    icons: displayIcon,
                    stripes: true
                }
            },
            getSelector: function (node) {
                return $.jstree.reference(node.reference).get_node(node.reference);
            },
            sort: function (table) {
                var $tree = $('#tree');
                var positions = {};
                $($tree.jstree().get_json($tree, {flat: true})).each(function (index, value) {
                    // var node = $("#tree").jstree().get_node(this.id);
                    // var lvl = node.parents.length;
                    // console.log('node index = ' + index + ' level = ' + lvl + ' id = ' + node.id);
                    positions[value.id] = index;
                });
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: page.siteRoot() + table + '/index',
                    data: {
                        action: 'sort',
                        data: positions,
                        _token: page.token()
                    }
                });
            },
            move: function (table, e, data) {
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: page.siteRoot() + table + '/index',
                    data: {
                        action: 'move',
                        id: data.node.id,
                        parentId: data.node.parent,
                        _token: page.token()
                    },
                    success: function () {
                        $.when(
                            tree.sort(table)
                        ).then(
                            $('#tree').jstree().refresh()
                        );
                    },
                    error: function (e) {
                        page.errorHandler(e);
                    }
                });
            },
            checkCallback: function (o, n, p, i, m, t, table) {
                // o - operation, n - node, p - node_parent, i - node_position, m - more, t - this
                // m.pos: 'b' - addBefore, 'a' - addAfter, 'i' - append
                // first | last | after | before
                // if(m && m.dnd && m.pos !== 'i') { return false; }
                // if(o === "move_node" || o === "copy_node") {
                //    if(this.get_node(p).id === '#') { return false; }
                // }
                var nNode = t.get_node(n),
                    pNode = t.get_node(p),
                    nType = nNode.type, // 节点类型
                    pType = pNode.type, // 父节点类型
                    nCorpId = nNode['corp_id'],
                    pCorpId = pNode['corp_id'],
                    grandParents, grandParentTypes, cNode, gpNode;
                if (o === "move_node" || o === "copy_node") {
                    switch (table) {
                        case 'departments':
                            switch (nType) {
                                case 'company':
                                    return pType === 'root';
                                case 'corp':
                                    return pType === 'company';
                                case 'school':
                                    return pType === 'corp' && nCorpId === pCorpId;
                                case 'grade':
                                    if (nCorpId !== pCorpId) { return false; }
                                    switch (pType) {
                                        case 'school':
                                            return true;
                                        case 'other':
                                            // parents of all levels of the current node
                                            grandParents = pNode.parents;
                                            grandParentTypes = [];
                                            $.each(grandParents, function () {
                                                gpNode = $('#tree').jstree(true).get_node(this, false);
                                                grandParentTypes.push(gpNode.type);
                                            });
                                            if (
                                                $.inArray('grade', grandParentTypes) > -1 ||
                                                $.inArray('class', grandParentTypes) > -1
                                            ) {
                                                return false;
                                            }
                                            return $.inArray('school', grandParentTypes) > -1;
                                        default:
                                            return false;
                                    }
                                // return $.inArray(pType, ['school', 'other']) > -1;
                                case 'class':
                                    if (nCorpId !== pCorpId) { return false; }
                                    switch (pType) {
                                        case 'grade':
                                            return true;
                                        case 'other':
                                            grandParents = pNode.parents;
                                            grandParentTypes = [];
                                            $.each(grandParents, function () {
                                                gpNode = $('#tree').jstree(true).get_node(this, false);
                                                grandParentTypes.push(gpNode.type);
                                            });
                                            return $.inArray('grade', grandParentTypes) > -1;
                                        default:
                                            return false;
                                    }
                                case 'other':
                                    if (nCorpId !== pCorpId) { return false; }
                                    var children = nNode.children_d,
                                        childrenTypes = [];
                                    $.each(children, function () {
                                        cNode = $('#tree').jstree(true).get_node(this, false);
                                        childrenTypes.push(cNode.type);
                                    });
                                    switch (pType) {
                                        case 'school':
                                            if ($.inArray('grade', childrenTypes) > -1) {
                                                return true;
                                            }
                                            return !($.inArray('class', childrenTypes) > -1);
                                        case 'grade':
                                            return !($.inArray('grade', childrenTypes) > -1);
                                        case 'class':
                                            return !($.inArray('class', childrenTypes) > -1)
                                                && !($.inArray('grade', childrenTypes) > -1);
                                        case 'other':
                                            grandParents = pNode.parents;
                                            grandParentTypes = [];
                                            $.each(grandParents, function () {
                                                gpNode = $('#tree').jstree(true).get_node(this, false);
                                                grandParentTypes.push(gpNode.type);
                                            });
                                            // has neither grades nor classes
                                            if (
                                                !($.inArray('class', grandParentTypes) > -1) &&
                                                !($.inArray('grade', grandParentTypes) > -1)
                                            ) {
                                                if (
                                                    !($.inArray('class', childrenTypes) > -1) &&
                                                    !($.inArray('grade', childrenTypes) > -1)
                                                ) {
                                                    return true;
                                                }
                                                return $.inArray('grade', childrenTypes) > -1;
                                            }
                                            // has grades but no classess
                                            if (
                                                $.inArray('grade', grandParentTypes) > -1 &&
                                                !($.inArray('class', grandParentTypes) > -1)
                                            ) {
                                                return $.inArray('grade', childrenTypes) <= -1;
                                            }
                                            // has classes but no grades
                                            if (
                                                $.inArray('class', grandParentTypes) > -1 &&
                                                !$.inArray('grade', grandParentTypes) > -1
                                            ) {
                                                return !($.inArray('class', childrenTypes) > -1);
                                            }
                                            break;
                                        default:
                                            return false;
                                    }
                                    // return $.inArray(pType, ['school', 'grade', 'class', 'other']) > -1;
                                    break;
                                default:
                                    return false;
                            }
                            break;
                        case 'menus':
                            switch (nType) {
                                case 'company':
                                    return pType === 'root';
                                case 'corp':
                                    return pType === 'company';
                                case 'school':
                                    return pType === 'corp' && nCorpId === pCorpId;
                                default:
                                    return $.inArray(pType, ['company', 'corp', 'school', 'other', 'root']) > -1;
                            }
                        default:
                            return false;
                    }
                }
                return true;
            },
            initJsTree: function (callback) {
                if (!($.fn.jstree)) {
                    page.loadCss(plugins.jstree.css);
                    $.getMultiScripts([plugins.jstree.js]).done(function () {
                        callback();
                    })
                } else {
                    callback();
                }
            },
            customMenu: function (node, table, $dialog, $activeTabPane) {
                var create = {
                    label: '创建',
                    icon: 'fa fa-plus',
                    action: function (node) {
                        var url = table + '/create/' + tree.getSelector(node).id;
                        page.getTabContent($activeTabPane, url);
                    }
                };
                var edit = {
                    label: '编辑',
                    icon: 'fa fa-edit',
                    action: function (node) {
                        var url = table + '/edit/' + tree.getSelector(node).id;
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
                    _disabled: function (node) {
                        var children = tree.getSelector(node).children_d;
                        var type, disabled = false;
                        $.each(children, function () {
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
                        var url = table + '/sort/' + tree.getSelector(node).id;
                        page.getTabContent($activeTabPane, url);
                    }
                };
                switch (table) {
                    case 'departments':
                        switch (node.type) {
                            case 'school':
                            case 'grade':
                            case 'class':
                                return {createItem: create};
                            case 'other':
                                return {createItem: create, renameItem: edit, delItem: del};
                            default:
                                return {};
                        }
                    case 'menus':
                        switch (node.type) {
                            case 'root':
                            case 'company':
                            case 'corp':
                            case 'school':
                                return {createItem: create};
                            case 'other':
                                if (node.children.length === 0) {
                                    return {createItem: create, renameItem: edit, delItem: del, rankTabs: rank};
                                }
                                return {createItem: create, renameItem: edit, delItem: del};
                            default:
                                return {}
                        }
                    default:
                        return {};
                }
            },
            index: function (table) {
                page.unbindEvents();
                $(document).off('click', '#sort');
                var $tree = $('#tree');
                var buildTree = function () {
                    $tree.jstree({
                        core: {
                            themes: tree.themes(table !== 'menus'),
                            expand_selected_onload: true,
                            check_callback: function (o, n, p, i, m) {
                                return tree.checkCallback(o, n, p, i, m, this, table);
                            },
                            multiple: false,
                            animation: 0,
                            data: {
                                url: page.siteRoot() + table + '/index',
                                type: 'POST',
                                dataType: 'json',
                                data: function (node) {
                                    return {
                                        action: 'tree',
                                        id: node.id,
                                        _token: page.token()
                                    };
                                }
                            },
                            error: function (e) {
                                page.errorHandler(e);
                            }
                        },
                        plugins: ['contextmenu', 'dnd', 'wholerow', 'unique', 'types'],
                        types: tree.options.departmentTypes,
                        contextmenu: {
                            items: function (node) {
                                return tree.customMenu(
                                    node, table, $('#modal-dialog'),
                                    $('#tab_' + page.getActiveTabId())
                                );
                            }
                        }
                    }).on('loaded.jstree', function () {
                        // 展开所有节点
                        // $tree.jstree('open_all');
                        // 展开第一级节点
                        $tree.jstree('open_node', $('#' + $tree.jstree(true).get_node('#').children[0]));
                        tree.sort(table);
                    }).on('move_node.jstree', function (e, data) {
                        return tree.move(table, e, data);
                    });
                };
                tree.initJsTree(buildTree);
                $('#confirm-delete').on('click', function () {
                    $.ajax({
                        type: 'DELETE',
                        dataType: 'json',
                        url: page.siteRoot() + table + '/delete/' + nodeid,
                        data: {_token: page.token()},
                        success: function (result) {
                            page.inform('删除节点', result.message, page.success);
                            $.when(
                                tree.sort(table)
                            ).done(
                                $tree.jstree().refresh()
                            );
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },

            // 部门及联系人列表
            contact: function (uri, type) {
                var $selectedDepartmentIds = $('#selected-node-ids'),
                    selectedNodes = $selectedDepartmentIds.val(),
                    selectedDepartmentIds = typeof selectedNodes !== 'undefined' ? selectedNodes.split(',') : [];

                $('.main-form').hide();
                $('#contacts').show();
                $('#tree').data('jstree', false).empty().jstree({
                    selectedNodes: selectedNodes,
                    core: {
                        themes: tree.themes(true),
                        multiple: true,
                        animation: 0,
                        expand_selected_onload: true,
                        check_callback: true,
                        data: {
                            url: page.siteRoot() + uri,
                            type: 'POST',
                            dataType: 'json',
                            data: function (node) {
                                return {
                                    id: node.id, _token: page.token()
                                }
                            }
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    },
                    checkbox: {
                        keep_selected_style: false,
                        three_state: false,
                        tie_selection: false,
                    },
                    plugins: ['types', 'search', 'checkbox', 'wholerow'],
                    types: type === 'department' ? tree.options.departmentTypes : tree.options.contactTypes
                }).on('check_node.jstree', function (node, selected) {
                    // if (!$('#' + selected.node.id + '_anchor :nth-child(1)').hasClass('jstree-checkbox')) {
                    //     return false;
                    // }
                    // 将选中的节点增加到右侧列表
                    var nodeHtml =
                        '<li id="tree' + selected.node.id + '">' +
                            '<span class="handle ui-sortable-handle">' +
                                '<i class="' + selected.node.icon + '"></i>' +
                            '</span>' +
                            '<span class="text">' + selected.node.text + '</span>' +
                            '<div class="tools">' +
                                '<i class="fa fa-close remove-node"></i>' +
                                '<input type="hidden" value="' + selected.node.id + '"/>' +
                            '</div>' +
                        '</li>';
                    $('.todo-list').append(nodeHtml);
                }).on('uncheck_node.jstree', function (node, selected) {
                    if (!$('#' + selected.node.id + '_anchor :nth-child(1)').hasClass('jstree-checkbox')) {
                        return false;
                    }
                    $('#tree' + selected.node.id).remove();
                }).on('loaded.jstree', function () {
                    var $tree = $('#tree'),
                        rootId = $tree.jstree(true).get_node('#').children[0];

                    // 展开第一级节点
                    $tree.jstree('open_node', $('#' + rootId));
                    // $tree.jstree('open_all');
                    // 初始化 根据后台数据节点数组 选中
                    $tree.jstree().check_node(selectedDepartmentIds);
                    $($tree.jstree(true).get_json($tree, {flat: true})).each(function () {
                        var node = $("#tree").jstree(true).get_node(this.id, false),
                            $node = $('#' + node.id);

                        if (node.original.selectable !== 1) {
                            $node.find('i.jstree-checkbox').removeClass();
                        } else {
                            $node.find('i[class=""]').addClass('jstree-icon jstree-checkbox');
                        }
                    });
                });
            },
            unbindEvents: function () {
                $(document).off('click', '.remove-node');
                $(document).off('click', '.remove-selected');
                $(document).off('click', '#save');
                $(document).off('click', '#cancel');
                $('#search').unbind('keyup');
                $('#choose').unbind('click');
            },
            purge: function () {
                $(document).on('click', '.remove-selected', function () {
                    var $selectedDepartmentIds = $('#selected-node-ids'),
                        // 获取隐藏域的input
                        nodeId = $(this).parents('button').find('input').val(),
                        // 转换成数组
                        selectedDepartmentIds = $selectedDepartmentIds.val().split(',');

                    //清除数组的 上面选中的部门id 返回还是数组
                    for (var i = 0; i < selectedDepartmentIds.length; i++) {
                        if (selectedDepartmentIds[i] === nodeId) {
                            selectedDepartmentIds.splice(i, 1);
                            break;
                        }
                    }
                    // 删除指定的部门
                    $(this).parents('button').remove();
                    //将删除后的数组转换成字符串 并赋值回input 方便在上面初始化中 初始 已选择的部门
                    $selectedDepartmentIds.val(selectedDepartmentIds.toString());
                    //同时删除 右侧列表 选中的节点
                    $('#tree' + nodeId).remove();
                    // 让某一个节点取消选中
                    // department.$tree.jstree().deselect_node([nodeId]);
                });
            },
            choose: function (uri, type) {
                $('#choose').on('click', function () {
                    tree.contact(uri, type);
                });
            },
            search: function () {
                $('#search').keyup(function () {
                    if (tree.to) {
                        clearTimeout(tree.to);
                    }
                    tree.to = setTimeout(function () {
                        var v = $('#search').val();
                        $('#tree').jstree(true).search(v);
                    }, 250);
                });
            },
            save: function () {
                $(document).on('click', '#retain', function () {
                    var nodeArray = [],
                        $tree = $('#tree'),
                        $selectedDepartmentIds = $('#selected-node-ids'),
                        // 点击保存时获取所有选中的节点 返回数组
                        selectedNodes = $tree.jstree().get_checked(),
                        $checkedNodes = $('#checked-nodes');

                    $checkedNodes.empty();
                    for (var i = 0; i < selectedNodes.length; i++) {
                        // 通过id查找节点
                        var node = $tree.jstree("get_node", selectedNodes[i]),
                            checkedNode =
                                '<button type="button" class="btn btn-flat" style="margin-right: 5px;margin-bottom: 5px">' +
                                    '<i class="' + node.icon + '"> ' + node.text + '</i> ' +
                                    '<i class="fa fa-close remove-selected"></i>' +
                                    '<input type="hidden" name="selectedDepartments[]" value="' + node.id + '"/>' +
                                '</button>';
                        $checkedNodes.append(checkedNode);
                        nodeArray[i] = node.id;
                    }
                    //更新隐藏域的 选中的id数组input 方便 弹出树形页面时默认选中
                    $selectedDepartmentIds.val(nodeArray.toString());
                    //保存后清空右侧 选中的节点列表
                    $('.todo-list').empty();
                    $tree.empty().jstree('destroy');
                    $('.main-form').show();
                    $('#contacts').hide();
                });
            },
            closeTree: function () {
                $(document).off('click', '.close-targets').on('click', '.close-targets',
                    function () { tree.close(); }
                )
            },
            cancel: function () {
                $(document).off('click', '#revoke').on('click', '#revoke',
                    function () { tree.close(); }
                );
            },
            close: function () {
                $('#tree').jstree('destroy');
                $('.todo-list').empty();
                $('#contacts').hide();
                $('.main-form').show();
            },
            remove: function () {
                $(document).on('click', '.remove-node', function () {
                    var nodeId = $(this).next().val();

                    $(this).parents('li').toggle('slow');
                    $('#tree').jstree().uncheck_node([nodeId]);
                });
            },
            list: function (uri, type) {
                // 取消所有事件绑定
                tree.unbindEvents();
                // 部门树页面 的取消按钮
                tree.cancel();
                tree.closeTree();
                // 点击教职员工编辑表单中的删除部门按钮
                tree.purge();
                // 点击表单中的部门修改按钮
                $('#tree').empty();
                $('.todo-list').empty();
                var init = function (uri, type) {
                    // 初始化“修改按钮”
                    tree.choose(uri, type);
                    // 初始化节点搜索功能
                    tree.search();
                    // 点击右侧选中节点中的删除图标
                    tree.remove();
                    // 初始化保存选中节点的功能
                    tree.save();
                };
                if (!($.fn.jstree) || !($.fn.select2) || !($.fn.iCheck)) {
                    var scripts = [
                        plugins.jstree.js,
                        plugins.select2.js,
                        plugins.icheck.js
                    ];
                    $.getMultiScripts(scripts).done(function () {
                        var $cip = $('#cip');
                        $cip.after($("<link/>", {
                            rel: "stylesheet", type: "text/css",
                            href: page.siteRoot() + plugins.jstree.css
                        })).after($("<link/>", {
                            rel: "stylesheet", type: "text/css",
                            href: page.siteRoot() + plugins.select2.css
                        })).after($("<link/>", {
                            rel: "stylesheet", type: "text/css",
                            href: page.siteRoot() + plugins.icheck.css
                        }));
                        init(uri, type);
                    });
                } else {
                    init(uri, type);
                }
            },
        };

        return {
            manage: tree.index,
            list: tree.list,
            options: tree.options,
            initTree: tree.initJsTree
        };
    };
})(jQuery);