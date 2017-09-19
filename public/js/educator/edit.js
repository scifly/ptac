$(crud.edit('formEducator', 'educators'));
// $(function () {
//     alert(1);
// });
var $tbody = $("#mobileTable").find("tbody");
var $tbody2 = $("#classTable").find("tbody");
var n = 0;

$tbody.find('tr:nth-last-child(1)').find('button').removeClass('btn-remove').addClass('btn-add');
$tbody.find('tr:nth-last-child(1)').find('i').removeClass('fa-minus').addClass('fa-plus');
$tbody2.find('tr:nth-last-child(1)').find('button').removeClass('btn-class-remove').addClass('btn-class-add');
$tbody2.find('tr:nth-last-child(1)').find('i').removeClass('fa-minus').addClass('fa-plus');
// 手机号
$(document).on('click', '.btn-add', function (e) {
    e.preventDefault();
    n++;
    // add html
    $tbody.append(
        '<tr><td><input type="text" class="form-control" placeholder="（请输入手机号码）" name="mobile[mobile][k' + n + ']" value=""></td>' +
        '<td style="text-align: center"><input type="radio" class="minimal" name="mobile[isdefault]" value="k' + n + '"></td>' +
        '<td style="text-align: center"><input type="checkbox" class="minimal" name="mobile[enabled][k' + n + ']"></td>' +
        '<td style="text-align: center"><button class="btn btn-box-tool btn-add" type="button"><i class="fa fa-plus text-blue"></i></button></td></tr>'
    );
    // icheck init
    $tbody.find('input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });
    $tbody.find('input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });
    $tbody.find('tr:not(:last) .btn-add')
        .removeClass('btn-add').addClass('btn-remove')
        .html('<i class="fa fa-minus text-blue"></i>');
}).on('click', '.btn-remove', function (e) {
    $(this).parents('tr:first').remove();
    e.preventDefault();
    return false;
});
// 班级、科目
$(document).on('click', '.btn-class-add', function (e) {
    e.preventDefault();

    var html = $tbody2.find('tr').last().clone();
    html.find('span.select2').remove();
    // 删除插件初始化增加的html
    $tbody2.append(html);
    // select2 init
    $('select').select2();
    // 加减切换
    $tbody2.find('tr:not(:last) .btn-class-add')
        .removeClass('btn-class-add').addClass('btn-class-remove')
        .html('<i class="fa fa-minus text-blue"></i>');
}).on('click', '.btn-class-remove', function (e) {
    // 删除元素
    $(this).parents('tr:first').remove();
    e.preventDefault();
    return false;
});

//部门
//树形图 jstree 左边
var $tree = $('#department-tree');
//教职员工 编辑表单
var $form = $('.form-horizontal');
//教职员工 编辑表单 保存取消按钮
var $btn = $('.box-footer');
//整个 部门选择的页面
var $treeBox = $('.tree-box');
//部门选择-选择好的右边节点列表
var $todoList = $('.todo-list');
//教职员工 编辑表单 选择好的部门列表
var $checkedTreeNodes = $('#department-nodes-checked');
//点击 表单中的部门修改按钮
$tree.empty();
$todoList.empty();
$('#add-department').on('click', function() {
    // $tree.jstree("refresh");
    // $tree.jstree("destroy");
    //获取 后台传过来的 已选择的部门 input 数组
    var selectedNodes = $('#selectedDepartmentIds').val();
    var selectedDepartmentIds = selectedNodes.split(',');
// console.log(selectedNodes,selectedDepartmentIds);
    $btn.hide();
    $form.hide();
    $treeBox.show();
    //部门树形图中的保存取消按钮
    $('.tree-box .box-footer').show();

    $tree.data('jstree', false).empty();
    $tree.jstree({
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
                url: 'http://sandbox.dev:8080/ptac/public/educators/edit/20',
                type: 'POST',
                dataType: 'json',
                data: function (node) {
                    return {id: node.id, _token: $('#csrf_token').attr('content')}
                }
            }
        },
        checkbox: {
            keep_selected_style : false,
            three_state: false
        },
        plugins: ['types', 'search', 'checkbox', 'wholerow'],
        types: {
            '#': { "icon": 'glyphicon glyphicon-flash' },
            'root': { "icon": 'fa fa-sitemap' },
            'company': { "icon": 'fa fa-building' },
            'corp': { "icon": 'fa fa-weixin' },
            'school': { "icon": 'fa fa-university' },
            'grade': { "icon": 'fa fa-users' },
            'class': { "icon": 'fa fa-user' },
            'other': { "icon": 'fa fa-list' }
        }
    }).on('select_node.jstree', function(node, selected) {
        // alert(1);
        // alert($(".jstree ").length);
        //选中事件 将选中的节点增|加到右边列表
        console.log(selected);
        var node = '<li id="tree'+selected.node.id+'">' +
            '<span class="handle ui-sortable-handle">' +
            '<i class="'+selected.node.icon+'"></i>' +
            '</span>' +
            '<span class="text">'+selected.node.text+'</span>' +
            '<div class="tools">' +
            '<i class="fa fa-close close-node"></i>' +
            '<input type="hidden" value="'+selected.node.id+'"/>' +
            '</div>' +
            '</li>';
        $todoList.append(node);
    }).on('deselect_node.jstree', function (node, selected, event) {
        // alert(2);
        //取消选中事件 将列表中的 节点 移除
        // console.log('++' + node, selected);
        var nodeId = '#tree'+selected.node.id;
        var deselectNode = $(nodeId);
        deselectNode.remove();

    }).on('loaded.jstree', function () {
        console.log(selectedDepartmentIds);
        //展开所有节点
        $tree.jstree('open_all');

        //初始化 根据后台数据节点数组 选中
        $tree.jstree().select_node(selectedDepartmentIds);
    })
});
//节点搜索功能
var to = false;
$('#search_node').keyup(function () {
    if (to) {
        clearTimeout(to);
    }
    to = setTimeout(function () {
        var v = $('#search_node').val();
        $tree.jstree(true).search(v);
    }, 250);
});
//右侧选中节点中的 删除图标 点击后移除本身并且将左侧取消选中
$(document).on('click','.close-node',function () {
    var nodeId=$(this).parents('li').find('input').val();
    console.log("123123");
    $(this).parents('li').remove();
    $tree.jstree().deselect_node([nodeId]);
});
//保存选中的节点
$(document).on('click','#save-nodes',function () {
    var nodeArray = new Array();
    //点击保存时获取所有选中的节点 返回数组
    var selectedNodes = $tree.jstree().get_selected();
    $checkedTreeNodes.empty();
    for(var i = 0;i < selectedNodes.length; i++) {
        //通过id查找节点
        var node=$tree.jstree("get_node", selectedNodes[i]);
        console.log('save--'+node);
        var checkedNode = '<button type="button" class=btn btn-flat" style="margin-right: 5px;margin-bottom: 5px">' +
            '<i class="'+node.icon+'"></i>'+node.text+
            '<i class="fa fa-close close-selected"></i>' +
            '<input type="hidden" name="selectedDepartments[]" value="'+node.id+'"/>' +
            '</button>';
        // $("#add-department").after(checkedNode);
        $checkedTreeNodes.append(checkedNode);
        nodeArray[i] = node.id;
    }
    //更新隐藏域的 选中的id数组input 方便 弹出树形页面时默认选中
    $('#selectedDepartmentIds').val(nodeArray.toString());
    //保存后清空右侧 选中的节点列表
    $todoList.empty();
    $tree.empty();
    $tree.jstree('destroy');
    $btn.show();
    $form.show();
    $treeBox.hide();
    //部门树形图中的保存取消按钮
    $('.tree-box .box-footer').hide();

});
//点击 教职员工编辑表单中的 删除部门
$(document).on('click','.close-selected',function () {
    //获取隐藏域的input
    var nodeId=$(this).parents('button').find('input').val();
    // 删除指定的部门
    $(this).parents('button').remove();

    //获取到后台的 当前用户的所有部门 字符串
    var selectedNodes = $('#selectedDepartmentIds').val();
    //转换成数组
    var selectedDepartmentIds = selectedNodes.split(',');
    //清除数组的 上面选中的部门id 返回还是数组
    for(var i=0; i<selectedDepartmentIds.length; i++) {
        if(selectedDepartmentIds[i] == nodeId) {
            selectedDepartmentIds.splice(i, 1);
            break;
        }
    }
    //将删除后的数组转换成字符串 并赋值回input 方便在上面初始化中 初始 已选择的部门
    $('#selectedDepartmentIds').val(selectedDepartmentIds.toString());
    //同时删除 右侧列表 选中的节点
    var treeNodeId = '#tree'+nodeId;
    var deselectNode = $(treeNodeId);
    deselectNode.remove();
    // $(this).parents('input').remove();
    // $tree.jstree().deselect_node([nodeId]);
});
//部门树页面 的取消按钮
$(document).on('click','#cancel-nodes',function () {
    $btn.show();
    $form.show();
    $treeBox.hide();
    $('.tree-box .box-footer').hide();
    $tree.jstree('destroy');
    $todoList.empty();
});





