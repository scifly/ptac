//# sourceURL=contacts.js

var contacts = {
    to: 0,
    $tree: function () {
        // 树形图 jstree 左边
        return $('#contacts-tree');
    },
    $form: function () {
        // 消息中心 编辑表单
        return $('#message');
    },
    $footer: function () {
        //部门树的 保存取消按钮
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
        $(document).off('click', '#save-attachment');
        $(document).off('click', '#cancel-attachment');
        $('#search_node').unbind('keyup');
        $('#add-attachment').unbind('click');
    },
    //部门树 右侧删除按钮 删除后左侧取消选中
    remove: function () {
        $(document).on('click', '.remove-node', function () {
            var nodeId = $(this).parents('li').find('input').val();
            $(this).parents('li').remove();
            contacts.$tree().jstree().deselect_node([nodeId]);
        });
    },
    //表单上 删除节点方法 待定
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
        });
    },
    tree: function (uri) {
        var $selectedDepartmentIds = $('#selectedDepartmentIds');
        // 获取 后台传过来的 已选择的部门 input 数组
        var selectedNodes = $selectedDepartmentIds.val();
        var selectedDepartmentIds = selectedNodes.split(',');

        contacts.$footer().hide();
        contacts.$form().hide();
        contacts.$treeBox().show();
        //部门树形图中的保存取消按钮
        $('.tree-box .box-footer').show();
        contacts.$tree().data('jstree', false).empty();
        var loadTree = function() {
            contacts.$tree().jstree({
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
                types: {
                    '#': {"icon": 'fa fa-tree'},
                    'dept': {"icon": 'fa fa-folder'},
                    'user': {"icon": 'fa fa-user'},
                }
            }).on('select_node.jstree', function (node, selected) {

                //选中事件 将选中的节点增|加到右边列表
                var nodeHtml = '<li id="tree' + selected.node.original.id + '">' +
                    '<span class="handle ui-sortable-handle">' +
                    '<i class="' + selected.node.icon + '"></i>' +
                    '</span>' +
                    '<span class="text">' + selected.node.text + '</span>' +
                    '<div class="tools">' +
                    '<i class="fa fa-close remove-node"></i>' +
                    '<input type="hidden" value="' + selected.node.original.id + '"/>' +
                    '</div>' +
                    '</li>';
                contacts.$todoList().append(nodeHtml);
            }).on('deselect_node.jstree', function (node, selected) {
                //取消选中事件 将列表中的 节点 移除
                var nodeId = '#tree' + selected.node.id;
                var deselectNode = $(nodeId);
                deselectNode.remove();
            }).on('loaded.jstree', function () {
                //展开所有节点
                contacts.$tree().jstree('open_all');
                //初始化 根据后台数据节点数组 选中
                contacts.$tree().jstree().select_node(selectedDepartmentIds);
            })
        };
        if (typeof tree === 'undefined') {
            $.getMultiScripts(['js/tree.crud.js'], page.siteRoot())
                .done(function() { loadTree(); });
        } else { loadTree(); }
    },
    modify: function (uri) {
        $('#add-attachment').on('click', function () {
            contacts.tree(uri);
        });
    },
    search: function () {
        $('#search_node').keyup(function () {
            if (contacts.to) {
                clearTimeout(contacts.to);
            }
            contacts.to = setTimeout(function () {
                var v = $('#search_node').val();
                contacts.$tree.jstree(true).search(v);
            }, 250);
        });
    },
    save: function () {
        $(document).on('click', '#save-attachment', function () {
            var nodeArray = [];
            var $selectedDepartmentIds = $('#selectedDepartmentIds');

            //点击保存时获取所有选中的节点 返回数组
            var selectedNodes = contacts.$tree().jstree().get_selected();
            contacts.$checkedTreeNodes().empty();
            for (var i = 0; i < selectedNodes.length; i++) {
                //通过id查找节点
                var node = contacts.$tree().jstree("get_node", selectedNodes[i]);
                console.log(node);

                var checkedNode = '<button type="button" class="btn btn-flat" style="margin-right: 5px;margin-bottom: 5px">' +
                    '<i class="' + node.icon + '"></i>' + node.text +
                    '<i class="fa fa-close close-selected"></i>' +
                    '<input type="hidden" name="selectedDepartments[' + node.original.role + '][]" value="' + node.id + '"/>' +
                    '</button>';
                contacts.$checkedTreeNodes().append(checkedNode);
                nodeArray[i] = node.id;
            }
            //更新隐藏域的 选中的id数组input 方便 弹出树形页面时默认选中
            $selectedDepartmentIds.val(nodeArray.toString());

            //保存后清空右侧 选中的节点列表
            contacts.$todoList().empty();
            contacts.$tree().empty();
            contacts.$tree().jstree('destroy');
            contacts.$footer().show();
            contacts.$form().show();
            contacts.$treeBox().hide();
            //部门树形图中的保存取消按钮
            $('.tree-box .box-footer').hide();
        });
    },
    cancel: function () {
        $(document).on('click', '#cancel-attachment', function () {
            contacts.$footer().show();
            contacts.$form().show();
            contacts.$treeBox().hide();
            $('.tree-box .box-footer').hide();
            contacts.$tree().jstree('destroy');
            contacts.$todoList().empty();
        });
    },
    init: function (uri) {
        // 取消所有事件绑定
        contacts.unbindEvents();
        // 部门树页面 的取消按钮
        contacts.cancel();
        // 点击 教职员工编辑表单中的 删除部门
        contacts.purge();
        // 点击表单中的部门修改按钮
        contacts.$tree().empty();
        contacts.$todoList().empty();
        var init = function() {
            // 初始化“修改按钮”
            contacts.modify(uri);
            // 初始化节点搜索功能
            contacts.search();
            // 右侧选中节点中的 删除图标 点击后移除本身并且将左侧取消选中
            contacts.remove();
            // 初始化保存选中节点的功能
            contacts.save();
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
