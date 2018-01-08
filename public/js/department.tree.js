var dept = {
    to: 0,
    $tree: function () {
        // 树形图 jstree 左边
        return $('#department-tree');
    },
    $form: function () {
        // 教职员工 编辑表单
        return $('.form-horizontal');
    },
    $footer: function () {
        //教职员工 编辑表单 保存取消按钮
        return $('.box-footer');
    },
    $treeBox: function () {
        //整个 部门选择的页面
        return $('.tree-box');
    },
    $todoList: function () {
        //部门选择-选择好的右边节点列表
        return $('.todo-list');
    },
    $checkedTreeNodes: function () {
        //教职员工 编辑表单 选择好的部门列表
        return $('#department-nodes-checked');
    },
    unbindEvents: function() {
        $(document).off('click', '.remove-node');
        $(document).off('click', '.close-selected');
        $(document).off('click', '#save-nodes');
        $(document).off('click', '#cancel-nodes');
        $('#search_node').unbind('keyup');
        $('#add-department').unbind('click');
    },
    remove: function () {
        $(document).on('click', '.remove-node', function () {
            var nodeId = $(this).parents('li').find('input').val();
            $(this).parents('li').remove();
            dept.$tree().jstree().deselect_node([nodeId]);
        });
    },
    purge: function () {
        $(document).on('click', '.close-selected', function () {
            var $selectedDepartmentIds = $('#selectedDepartmentIds');
            //获取隐藏域的input
            var nodeId = $(this).parents('button').find('input').val();
            // 删除指定的部门
            $(this).parents('button').remove();
            //获取到后台的 当前用户的所有部门 字符串
            var selectedNodes = $selectedDepartmentIds.val();
            //转换成数组
            var selectedDepartmentIds = selectedNodes.split(',');
            //清除数组的 上面选中的部门id 返回还是数组
            for (var i = 0; i < selectedDepartmentIds.length; i++) {
                if (selectedDepartmentIds[i] === nodeId) {
                    selectedDepartmentIds.splice(i, 1);
                    break;
                }
            }
            //将删除后的数组转换成字符串 并赋值回input 方便在上面初始化中 初始 已选择的部门
            $selectedDepartmentIds.val(selectedDepartmentIds.toString());
            //同时删除 右侧列表 选中的节点
            var treeNodeId = '#tree' + nodeId;
            var deselectNode = $(treeNodeId);
            deselectNode.remove();
            // 让某一个节点取消选中
            // department.$tree.jstree().deselect_node([nodeId]);
        });
    },
    tree: function (uri) {
        var $selectedDepartmentIds = $('#selectedDepartmentIds');
        // 获取 后台传过来的 已选择的部门 input 数组
        var selectedNodes = $selectedDepartmentIds.val();
        var selectedDepartmentIds = selectedNodes.split(',');
        dept.$footer().hide();
        dept.$form().hide();
        dept.$treeBox().show();
        //部门树形图中的保存取消按钮
        $('.tree-box .box-footer').show();
        dept.$tree().data('jstree', false).empty();
        var loadTree = function() {
            dept.$tree().jstree({
                selectedNodes: selectedNodes,
                core: {
                    themes: {
                        variant: 'large',
                        dots: true,
                        icons: true,
                        stripes: true
                    },
                    multiple: true,
                    animation: 0,
                    data: {
                        url: page.siteRoot() + uri,
                        type: 'POST',
                        dataType: 'json',
                        data: function (node) {
                            console.log(node);

                            return {id: node.id, _token: $('#csrf_token').attr('content')}
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                    }
                },
                checkbox: {
                    keep_selected_style: false,
                    three_state: false
                },
                plugins: ['types', 'search', 'checkbox', 'wholerow'],
                types: tree.nodeTypes
            }).on('select_node.jstree', function (node, selected) {
                //选中事件 将选中的节点增|加到右边列表
                // console.log(selected);
                var nodeHtml = '<li id="tree' + selected.node.id + '">' +
                    '<span class="handle ui-sortable-handle">' +
                    '<i class="' + selected.node.icon + '"></i>' +
                    '</span>' +
                    '<span class="text">' + selected.node.text + '</span>' +
                    '<div class="tools">' +
                    '<i class="fa fa-close remove-node"></i>' +
                    '<input type="hidden" value="' + selected.node.id + '"/>' +
                    '</div>' +
                    '</li>';
                dept.$todoList().append(nodeHtml);
            }).on('deselect_node.jstree', function (node, selected) {
                //取消选中事件 将列表中的 节点 移除
                var nodeId = '#tree' + selected.node.id;
                var deselectNode = $(nodeId);
                deselectNode.remove();
            }).on('loaded.jstree', function () {
                console.log(selectedDepartmentIds);
                //展开所有节点
                dept.$tree().jstree('open_all');
                //初始化 根据后台数据节点数组 选中
                dept.$tree().jstree().select_node(selectedDepartmentIds);
            })
        };
        if (typeof tree === 'undefined') {
            $.getMultiScripts(['js/tree.crud.js'], page.siteRoot())
                .done(function() { loadTree(); });
        } else { loadTree(); }
    },
    modify: function (uri) {
        $('#add-department').on('click', function () {
            dept.tree(uri);
        });
    },
    search: function () {
        $('#search_node').keyup(function () {
            if (dept.to) {
                clearTimeout(dept.to);
            }
            dept.to = setTimeout(function () {
                var v = $('#search_node').val();
                dept.$tree().jstree(true).search(v);
            }, 250);
        });
    },
    save: function () {
        $(document).on('click', '#save-nodes', function () {
            var nodeArray = [];
            var $selectedDepartmentIds = $('#selectedDepartmentIds');
            //点击保存时获取所有选中的节点 返回数组
            var selectedNodes = dept.$tree().jstree().get_selected();
            dept.$checkedTreeNodes().empty();
            for (var i = 0; i < selectedNodes.length; i++) {
                //通过id查找节点
                var node = dept.$tree().jstree("get_node", selectedNodes[i]);
                console.log('save--' + node);
                var checkedNode = '<button type="button" class="btn btn-flat" style="margin-right: 5px;margin-bottom: 5px">' +
                    '<i class="' + node.icon + '"></i>' + node.text +
                    '<i class="fa fa-close close-selected"></i>' +
                    '<input type="hidden" name="selectedDepartments[]" value="' + node.id + '"/>' +
                    '</button>';
                // $("#add-department").after(checkedNode);
                dept.$checkedTreeNodes().append(checkedNode);
                nodeArray[i] = node.id;
            }
            //更新隐藏域的 选中的id数组input 方便 弹出树形页面时默认选中
            $selectedDepartmentIds.val(nodeArray.toString());
            //保存后清空右侧 选中的节点列表
            dept.$todoList().empty();
            dept.$tree().empty();
            dept.$tree().jstree('destroy');
            dept.$footer().show();
            dept.$form().show();
            dept.$treeBox().hide();
            //部门树形图中的保存取消按钮
            $('.tree-box .box-footer').hide();
        });
    },
    cancel: function () {
        $(document).on('click', '#cancel-nodes', function () {
            dept.$footer().show();
            dept.$form().show();
            dept.$treeBox().hide();
            $('.tree-box .box-footer').hide();
            dept.$tree().jstree('destroy');
            dept.$todoList().empty();
        });
    },
    init: function (uri) {
        // 取消所有事件绑定
        dept.unbindEvents();
        // 部门树页面 的取消按钮
        dept.cancel();
        // 点击 教职员工编辑表单中的 删除部门
        dept.purge();
        // 点击表单中的部门修改按钮
        dept.$tree().empty();
        dept.$todoList().empty();
        var init = function() {
            // 初始化“修改按钮”
            dept.modify(uri);
            // 初始化节点搜索功能
            dept.search();
            // 右侧选中节点中的 删除图标 点击后移除本身并且将左侧取消选中
            dept.remove();
            // 初始化保存选中节点的功能
            dept.save();
        };
        if (!($.fn.jstree) || !($.fn.select2) || !($.fn.iCheck)) {
            var scripts = [
                page.plugins.jstree.js,
                page.plugins.select2.js,
                page.plugins.icheck.js
            ];
            $.getMultiScripts(scripts, page.siteRoot())
                .done(function() {
                    var $cip = $('#cip');
                    $cip.after($("<link/>", {
                        rel: "stylesheet", type: "text/css",
                        href: page.siteRoot() + page.plugins.jstree.css
                    })).after($("<link/>", {
                        rel: "stylesheet", type: "text/css",
                        href: page.siteRoot() + page.plugins.select2.css
                    })).after($("<link/>", {
                        rel: "stylesheet", type: "text/css",
                        href: page.siteRoot() + page.plugins.icheck.css
                    }));
                    init(uri);
                });
        } else { init(uri); }
    }
};
