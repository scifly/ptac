// /**
//  * Created by Administrator on 2017-07-21 0021.
//  */
// $(crud.create('formEducator', 'educators'));
// var isDefault =
//     '<label for="mobile[isdefault][]">' +
//     // '<input name="mobile[isdefault][]" type="radio" id="mobile[isdefault][]" class="minimal">' +
//     '<input name="mobile[isdefault][]" type="radio" class="minimal">' +
//     '</label>';
// var enabled =
//     '<label for="mobile[enabled][]">' +
//     // '   <input name="mobile[enabled][]" type="checkbox" id="mobile[enabled][]" class="minimal">' +
//     '   <input name="mobile[enabled][]" type="checkbox" class="minimal">' +
//     '</label>';
// $(document).on('click', '.btn-add', function(e) {
//     e.preventDefault();
//
//     var $tbody = $('tbody');
//     var $row = $(this).parents('tr:first');
//     var $clone = $($row.clone()).appendTo($tbody);
//     $tbody.find('tr:last td:nth-child(2)').html(isDefault)
//         .find('input[type="radio"]').iCheck({
//         checkboxClass: 'icheckbox_minimal-blue',
//         radioClass: 'iradio_minimal-blue'
//     });
//     $tbody.find('tr:last td:nth-child(3)').html(enabled)
//         .find('input[type="checkbox"]').iCheck({
//         checkboxClass: 'icheckbox_minimal-blue',
//         radioClass: 'iradio_minimal-blue'
//     });
//     $clone.find('input[type="text"]').val('');
//     $tbody.find('tr:not(:last) .btn-add')
//         .removeClass('btn-add').addClass('btn-remove')
//         .html('<i class="fa fa-minus text-blue"></i>');
// }).on('click', '.btn-remove', function(e) {
//     $(this).parents('tr:first').remove();
//     e.preventDefault();
//     return false;
// });
/**
 * Created by Administrator on 2017-07-21 0021.
 */
$(crud.create('formEducator', 'educators'));
$(crud.mobile('formEducator', 0, 'POST', 'educators/store'));
var $tbody = $("#mobileTable").find("tbody");
var n = 0;
var id = $('#id').val();
var $formEducator = $('#formEducator');
$(document).off('click', '.btn-mobile-add');
$(document).off('click', '.btn-mobile-remove');
// 手机号

// 班级、科目
var $tbody2 = $("#classTable").find("tbody");
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
dept.init('educators/create');