$(crud.edit('formEducator', 'educators'));
// $(function () {
//     alert(1);
// });
var $tbody = $("#mobileTable").find("tbody");
var $tbody2 = $("#classTable").find("tbody");
var n = 0;
var id = $('#id').val();

/*
$tbody.find('tr:nth-last-child(1)').find('button').removeClass('btn-remove').addClass('btn-add');
$tbody.find('tr:nth-last-child(1)').find('i').removeClass('fa-minus').addClass('fa-plus');
$tbody2.find('tr:nth-last-child(1)').find('button').removeClass('btn-class-remove').addClass('btn-class-add');
$tbody2.find('tr:nth-last-child(1)').find('i').removeClass('fa-minus').addClass('fa-plus');
*/
$(document).off('click', '.btn-add');
$(document).off('click', '.btn-remove');
// 手机号
$(document).on('click', '.btn-add', function (e) {
    e.preventDefault();
    n++;
    // add html
    $tbody.append(
        '<tr><td><input class="form-control" placeholder="（请输入手机号码）" name="mobile[mobile][k' + n + ']" value="" ></td>' +
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
    var $mobile = $tbody.find('tr:last input[class="form-control"]');
    $('#formEducator').parsley().destroy();
    $mobile.attr('pattern', '/^1[0-9]{10}$/');
    // $mobile.attr('required', 'true');
    $('#formEducator').parsley();
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
dept.init('educators/edit/' + id);





