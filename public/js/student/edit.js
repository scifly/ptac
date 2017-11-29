page.edit('formStudent','students');
var $mContainer = $("#mobiles").find("tbody");
var n = 0;
var id = $('#id').val();
var $formEducator = $('#formCustodian');
var $mobileSize = $('#mobile-size').val();
$(document).off('click','.btn-mobile-add');

// 手机号
$(document).on('click', '.btn-mobile-add', function (e) {
    e.preventDefault();
    $mobileSize++;
    // add html
    $mContainer.append(
        '<tr>' +
        '<td>' +
            '<div class="input-group">' +
                '<div class="input-group-addon">' +
                    '<i class="fa fa-mobile"></i>' +
                '</div>' +
                '<input class="form-control" placeholder="（请输入手机号码）" ' +
                        'name="mobile['+ $mobileSize +'][mobile]" value="">' +
            '</div>' +
        '</td>' +
        '<td style="text-align: center">' +
            '<input type="radio" class="minimal" id="mobile[isdefault]" ' +
                    'name="mobile[isdefault]" value="' + $mobileSize + '">' +
        '</td>' +
        '<td style="text-align: center">' +
            '<input type="checkbox" class="minimal" name="mobile['+ $mobileSize +'][enabled]">' +
        '</td>' +
        '<td style="text-align: center">' +
            '<button class="btn btn-box-tool btn-add btn-mobile-add" type="button">' +
                '<i class="fa fa-plus text-blue" title="新增"></i>' +
        '</button>' +
        '</td>' +
        '</tr>'
    );
    // icheck init
    page.initICheck($mContainer);
    $mContainer.find('tr:not(:last) .btn-mobile-add')
        .removeClass('btn-mobile-add').addClass('btn-mobile-remove')
        .html('<i class="fa fa-minus text-blue" title="删除"></i>');
    var $mobile = $mContainer.find('tr:last input[class="form-control"]');
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