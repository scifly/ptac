// 初始化编辑相关事件
page.edit('formEducator', 'educators');

var size = $('#mobile-size').val();
var id = $('#id').val();
$(document).off('click', '.btn-class-add');
$(document).off('click', '.btn-class-remove');
// $(crud.mobile('formEducator',size, 'PUT', 'educators/update/'+id));
if (typeof crud === 'undefined') {
    $.getMultiScripts(['js/admin.crud.js'], page.siteRoot())
        .done(function() { crud.mobile('formEducator', size, 'PUT', 'educators/update/'+id); })
} else { crud.mobile('formEducator', size, 'PUT', 'educators/update/'+id); }
var $tbody2 = $("#classTable").find("tbody");
// 手机号
// $(crud.mobileMgmt('formEducator'));
// 班级、科目
var $class = $("#classes").find("tbody");
$(document).on('click', '.btn-class-add', function (e) {
    e.preventDefault();
    var html = $class.find('tr').last().clone();
    html.find('span.select2').remove();
    // 删除插件初始化增加的html
    $class.append(html);
    // select2 init
    page.initSelect2();
    // 加减切换
    $class.find('tr:not(:last) .btn-class-add')
        .removeClass('btn-class-add').addClass('btn-class-remove')
        .html('<i class="fa fa-minus text-blue"></i>');
    $('#user[group_id]').on('click',function(){
        alert(123);
    });
}).on('click', '.btn-class-remove', function (e) {
    // 删除元素
    $(this).parents('tr:first').remove();
    e.preventDefault();
    return false;
});

// 初始化部门树 相关事件
if (typeof dept === 'undefined') {
    $.getMultiScripts(['js/department.tree.js'], page.siteRoot())
        .done(function() { dept.init('educators/edit/' + id); })
} else { dept.init('educators/edit/' + id); }




