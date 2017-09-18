$(crud.edit('formEducator', 'educators'));
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
var $tree = $('#department-tree');
var $form = $('.form-horizontal');
var $btn = $('.box-footer');

$('#add-department').on('click', function() {
    var selectedNodes = $('#selectedDepartmentIds').val();
    var selectedDepartmentIds = selectedNodes.split(',');

    // console.log(selectedDepartmentIds);
    $btn.hide();
    $form.hide();
    // $tree.show();
    $('.tree-box').show();
    $('.tree-box .box-footer').show();
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
            three_state: false,
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
        //选中事件
        // console.log(selected);
        //点击保存时获取所有选中的节点 返回数组
        var selectNodes = $("#department-tree").jstree().get_selected();
        console.log(selectNodes);
    }).on('deselect_node.jstree', function (node, selected, event) {
        //取消选中事件
        console.log('++' + node, selected);

    }).on('loaded.jstree', function (selectedDepartmentIds) {
        //展开所有节点
        $tree.jstree('open_all');
        //初始化 根据后台数据节点数组 选中
        $("#department-tree").jstree().select_node(selectedDepartmentIds);
    })
});
var to = false;
$('#search_node').keyup(function () {
    if (to) {
        clearTimeout(to);
    }
    to = setTimeout(function () {
        var v = $('#search_node').val();
        $('#department-tree').jstree(true).search(v);
    }, 250);
});



