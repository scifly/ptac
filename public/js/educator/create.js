$(crud.create('formEducator', 'educators'));
$(crud.mobile('formEducator', 0, 'POST', 'educators/store'));
var $tbody = $("#mobileTable").find("tbody");
var n = 0;
var id = $('#id').val();
var $formEducator = $('#formEducator');

// 手机号

// 班级、科目
var $tbody2 = $("#classTable").find("tbody");
$(document).on('click', '.btn-class-add', function (e) {
    $(document).off('click', '.btn-class-add');
    $(document).off('click', '.btn-class-remove');
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
dept.init('educators/create');