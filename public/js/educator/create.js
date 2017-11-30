page.create('formEducator', 'educators');
var id = $('#id').val();
var $formEducator = $('#formStudent');
$(document).off('click', '.btn-class-add');
$(document).off('click', '.btn-class-remove');

if (typeof crud === 'undefined') {
    $.getMultiScripts(['js/admin.crud.js'], page.siteRoot())
        .done(function() { crud.mobile('formEducator', 0, 'POST', 'educators/store'); })
} else { crud.mobile('formEducator', 0, 'POST', 'educators/store'); }
// 手机号
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
}).on('click', '.btn-class-remove', function (e) {
    // 删除元素
    $(this).parents('tr:first').remove();
    e.preventDefault();
    return false;
});

//部门
if (typeof dept === 'undefined') {
    $.getMultiScripts(['js/department.tree.js'], page.siteRoot())
        .done(function() { dept.init('educators/create'); });
} else { dept.init('educators/create'); }