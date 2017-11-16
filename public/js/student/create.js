
page.create('formStudent','students');

var n = 0;
// 手机号码列表容器
var $mContainer = $("#mobileTable").find("tbody");
var id = $('#id').val();
var $formEducator = $('#formStudent');
$(document).off('click', '.btn-mobile-add');
$(document).off('click', '.btn-remove');
// 手机号
$(document).on('click', '.btn-mobile-add', function (e) {
    e.preventDefault();
    n++;
    // add mobile html
    $mContainer.append(
        '<tr><td><input class="form-control" placeholder="（请输入手机号码）" name="mobile['+ n +'][mobile]" value="" ></td>' +
        '<td style="text-align: center"><input type="radio" class="minimal" id="mobile[isdefault]" name="mobile[isdefault]" value="' + n + '"></td>' +
        '<td style="text-align: center"><input type="checkbox" class="minimal" name="mobile['+ n +'][enabled]"></td>' +
        '<td style="text-align: center"><button class="btn btn-box-tool btn-add btn-mobile-add" type="button"><i class="fa fa-plus text-blue"></i></button></td></tr>'
    );
    // icheck init
    $mContainer.find('input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });
    $mContainer.find('input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });
    $mContainer.find('tr:not(:last) .btn-mobile-add')
        .removeClass('btn-mobile-add').addClass('btn-mobile-remove')
        .html('<i class="fa fa-minus text-blue"></i>');
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

