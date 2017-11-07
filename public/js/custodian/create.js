page.create('formCustodian', 'custodians');

var n = 0;  // 监护人手机号码数量
var $mobiles = $("#mobiles").find("tbody");
var id = $('#id').val();
var $formCustodian = $('#formCustodian');
$(document).off('click', '.btn-mobile-add');
$(document).off('click', '.btn-remove');

/** 监护人手机号管理 */
$(document).on('click', '.btn-mobile-add', function (e) {
    e.preventDefault();
    n++;
    // insert html for adding another mobile number
    $mobiles.append(
        '<tr>' +
            '<td class="text-center">' +
                '<div class="input-group">' +
                    '<div class="input-group-addon">' +
                        '<i class="fa fa-mobile"></i>' +
                    '</div>' +
                    '<input class="form-control" placeholder="（请输入手机号码）" name="mobile['+ n +'][mobile]" value="">' +
                '</div>' +
            '</td>' +
            '<td class="text-center">' +
                '<input type="radio" class="minimal" id="mobile[isdefault]" name="mobile[isdefault]" value="' + n + '">' +
            '</td>' +
            '<td class="text-center">' +
                '<input type="checkbox" class="minimal" name="mobile['+ n +'][enabled]">' +
            '</td>' +
            '<td class="text-center">' +
                '<button class="btn btn-box-tool btn-add btn-mobile-add">' +
                    '<i class="fa fa-plus text-blue"></i>' +
                '</button>' +
            '</td>' +
        '</tr>'
    );
    // init iCheck plugin
    page.initICheck();
    $mobiles.find('tr:not(:last) .btn-mobile-add')
        .removeClass('btn-mobile-add').addClass('btn-mobile-remove')
        .html('<i class="fa fa-minus text-blue"></i>');
    var $mobile = $mobiles.find('tr:last input[class="form-control"]');
    // reinitialize parsley plugin
    $formCustodian.parsley().destroy();
    $mobile.attr('pattern', '/^1[0-9]{10}$/');
    $mobile.attr('required', 'true');
    $formCustodian.parsley();
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

/** 监护人学生关系管理 */
var $addPupil = $('#add-pupil');
$addPupil.on('click', function () {
    $('#pupils').modal({backdrop: true});
});
// var $tbody2 = $("#classTable").find("tbody");
// $(document).off('click','.btn-class-add');
// $(document).on('click', '.btn-class-add', function (e) {
//     e.preventDefault();
//     var html = $tbody2.find('tr').last().clone();
//     html.find('span.select2').remove();
//     // 删除插件初始化增加的html
//     $tbody2.append(html);
//     // select2 init
//     page.initSelect2();
//     // 加减切换
//     $tbody2.find('tr:not(:last) .btn-class-add')
//         .removeClass('btn-class-add').addClass('btn-class-remove')
//         .html('<i class="fa fa-minus text-blue"></i>');
// }).on('click', '.btn-class-remove', function (e) {
//     // 删除元素
//     $(this).parents('tr:first').remove();
//     e.preventDefault();
//     return false;
// });

// /** 监护人所属部门管理 */
// if (typeof dept === 'undefined') {
//     $.getMultiScripts(['js/department.tree.js'], page.siteRoot())
//         .done(function() { dept.init('custodians/create'); })
// } else { dept.init('custodians/create'); }