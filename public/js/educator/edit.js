// 初始化编辑相关事件
$(crud.edit('formEducator', 'educators'));
var $tbody = $("#mobileTable").find("tbody");
var $tbody2 = $("#classTable").find("tbody");
var n = 0;
var id = $('#id').val();
var $formEducator = $('#formEducator');
var $mobileSize = $('#mobile-size').val();
$(document).off('click', '.btn-add');
$(document).off('click', '.btn-remove');

// 手机号
$(document).on('click', '.btn-mobile-add', function (e) {
    e.preventDefault();
    $mobileSize++;
    // add html
    $tbody.append(
        '<tr><td><input class="form-control" placeholder="（请输入手机号码）" name="mobile['+ $mobileSize +'][mobile]" value="" ></td>' +
        '<td style="text-align: center"><input type="radio" class="minimal" id="mobile[isdefault]" name="mobile[isdefault]" value="' + $mobileSize + '"></td>' +
        '<td style="text-align: center"><input type="checkbox" class="minimal" name="mobile['+ $mobileSize +'][enabled]"></td>' +
        '<td style="text-align: center"><button class="btn btn-box-tool btn-add btn-mobile-add" type="button"><i class="fa fa-plus text-blue"></i></button></td></tr>'
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
    $tbody.find('tr:not(:last) .btn-mobile-add')
        .removeClass('btn-mobile-add').addClass('btn-mobile-remove')
        .html('<i class="fa fa-minus text-blue"></i>');
    var $mobile = $tbody.find('tr:last input[class="form-control"]');
    $formEducator.parsley().destroy();
    $mobile.attr('pattern', '/^1[0-9]{10}$/');
    $mobile.attr('required', 'true');
    $formEducator.parsley();
}).on('click', '.btn-mobile-remove', function (e) {

    $(this).parents('tr:first').remove();
    e.preventDefault();
    var $defaults = $('input[name="mobile[isdefault]"]');
    var defaultChecked = false;
    $.each($defaults, function () {
        if (typeof $(this).attr('checked') !== 'undefined') {
            defaultChecked = true;
            return false;
        }
    });
    if (!defaultChecked) {
        $($defaults[0]).iCheck('check');
    }
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

// 初始化部门树 相关事件
dept.init('educators/edit/' + id);





