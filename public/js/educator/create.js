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
var $tbody = $("#mobileTable").find("tbody");
var n = 0;
$(document).off('click', '.btn-add');
$(document).off('click', '.btn-remove');
// 手机号
$(document).on('click', '.btn-add', function (e) {
    e.preventDefault();
    n++;
    // add mobile html
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