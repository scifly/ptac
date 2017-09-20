/**
 * Created by Administrator on 2017-07-21 0021.
 */
$(crud.create('formMessage', 'messages'));

var $pre = $('.preview');
var $uploadFile = $('#uploadFile');
// 初始化
$uploadFile.fileinput({
    language: 'zh',
    theme: 'explorer',
    uploadUrl: "../wap_sites/uploadImages",
    uploadAsync: false,
    maxFileCount: 5,
    minImageWidth: 50, //图片的最小宽度
    minImageHeight: 50,//图片的最小高度
    maxImageWidth: 1000,//图片的最大宽度
    maxImageHeight: 1000,//图片的最大高度
    allowedFileExtensions: ['jpg', 'gif', 'png'],//接收的文件后缀
    fileActionSettings: {
        showRemove: true,
        showUpload: false,
        showDrag: false
    },
    uploadExtraData: {
        '_token': $('#csrf_token').attr('content')
    }
});
// 上传成功
$uploadFile.on("filebatchuploadsuccess", function (event, data, previewId, index) {
    // 填充数据
    var response = data.response.data;
    $.each(response, function (index, obj) {
        $pre.append('<div class="img-item"><img src="../../' + obj.path + '" id="' + obj.id + '"><div class="del-mask"><i class="delete glyphicon glyphicon-trash"></i></div></div>');
        $pre.append('<input type="hidden" name="media_ids[]" value="' + obj.id + '">');
    });
    // 成功后关闭弹窗
    setTimeout(function () {
        $('#modalPic').modal('hide');
    }, 800)
});

// modal关闭，内容清空
$('#modalPic').on('hide.bs.modal', function () {
    $uploadFile.fileinput('clear');
});
// 点击删除按钮
$('body').on('click', '.delete', function () {
    $(this).parent().parent().remove();
    $pre.append('<input type="hidden" name="del_ids[]" value="' + $(this).parent().siblings().attr('id') + '">');
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
$('#add-department').on('click', function () {
    var $selectedDepartmentIds = $('#selectedDepartmentIds');
    // $tree.jstree("refresh");
    // $tree.jstree("destroy");
    //获取 后台传过来的 已选择的部门 input 数组
    var selectedNodes = $selectedDepartmentIds.val();
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
            keep_selected_style: false,
            three_state: false
        },
        plugins: ['types', 'search', 'checkbox', 'wholerow'],
        types: {
            '#': {"icon": 'glyphicon glyphicon-flash'},
            'root': {"icon": 'fa fa-sitemap'},
            'company': {"icon": 'fa fa-building'},
            'corp': {"icon": 'fa fa-weixin'},
            'school': {"icon": 'fa fa-university'},
            'grade': {"icon": 'fa fa-users'},
            'class': {"icon": 'fa fa-user'},
            'other': {"icon": 'fa fa-list'}
        }
    }).on('select_node.jstree', function (node, selected) {
        // alert(1);
        // alert($(".jstree ").length);
        //选中事件 将选中的节点增|加到右边列表
        $todoList.html();
        console.log(selected);
        console.log(selected.selected);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '../messages/get_depart_users',
            data:{id:selected.node.id, _token: $('#csrf_token').attr('content')},
            success: function (result) {
                if(result.statusCode === 200){
                    $todoList.html(result.message);
                }
            }
        });
        // var nodeHtml = '<li id="tree' + selected.node.id + '">' +
        //     '<span class="handle ui-sortable-handle">' +
        //     '<i class="' + selected.node.icon + '"></i>' +
        //     '</span>' +
        //     '<span class="text">' + selected.node.text + '</span>' +
        //     '<div class="tools">' +
        //     '<i class="fa fa-close close-node"></i>' +
        //     '<input type="hidden" value="' + selected.node.id + '"/>' +
        //     '</div>' +
        //     '</li>';
        // $todoList.append(nodeHtml);
    }).on('deselect_node.jstree', function (node, selected, event) {
        //取消选中事件 将列表中的 节点 移除
        // console.log('++' + node, selected);
        // var nodeId = '#tree' + selected.node.id;
        // var deselectNode = $(nodeId);
        // deselectNode.remove();
        $todoList.empty();

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
$(document).on('click', '.close-node', function () {
    var nodeId = $(this).parents('li').find('input').val();
    console.log("123123");
    $(this).parents('li').remove();
    $tree.jstree().deselect_node([nodeId]);
});
//保存选中的节点
$(document).on('click', '#save-nodes', function () {
    var nodeArray = [];
    var $selectedDepartmentIds = $('#selectedDepartmentIds');
    //点击保存时获取所有选中的节点 返回数组
    var selectedNodes = $tree.jstree().get_selected();
    $checkedTreeNodes.empty();
    for (var i = 0; i < selectedNodes.length; i++) {
        //通过id查找节点
        var node = $tree.jstree("get_node", selectedNodes[i]);
        console.log('save--' + node);
        var checkedNode = '<button type="button" class="btn btn-flat" style="margin-right: 5px;margin-bottom: 5px">' +
            '<i class="' + node.icon + '"></i>' + node.text +
            '<i class="fa fa-close close-selected"></i>' +
            '<input type="hidden" name="selectedDepartments[]" value="' + node.id + '"/>' +
            '</button>';
        // $("#add-department").after(checkedNode);
        $checkedTreeNodes.append(checkedNode);
        nodeArray[i] = node.id;
    }
    //更新隐藏域的 选中的id数组input 方便 弹出树形页面时默认选中
    $selectedDepartmentIds.val(nodeArray.toString());
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
    // $(this).parents('input').remove();
    //让某一个节点取消选中
    // $tree.jstree().deselect_node([nodeId]);
});
//部门树页面 的取消按钮
$(document).on('click', '#cancel-nodes', function () {
    $btn.show();
    $form.show();
    $treeBox.hide();
    $('.tree-box .box-footer').hide();
    $tree.jstree('destroy');
    $todoList.empty();
});

