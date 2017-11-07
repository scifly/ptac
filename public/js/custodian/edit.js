page.edit('formCustodian', 'custodians');

var $tbody = $("#mobileTable").find("tbody");
// 监护人手机号码数量
var n = $('#count').val();
$(document).off('click','.btn-mobile-add');

/** 监护人手机号管理 */
$(document).on('click', '.btn-mobile-add', function (e) {
    e.preventDefault();
    n++;
    // insert html for adding additional mobile number
    $tbody.append(
        '<tr>' +
            '<td>' +
                '<input class="form-control" placeholder="（请输入手机号码）" name="mobile['+ n +'][mobile]" value="" >' +
            '</td>' +
            '<td style="text-align: center">' +
                '<input type="radio" class="minimal" id="mobile[isdefault]" name="mobile[isdefault]" value="' + n + '">' +
            '</td>' +
            '<td style="text-align: center">' +
                '<input type="checkbox" class="minimal" name="mobile['+ n +'][enabled]">' +
            '</td>' +
            '<td style="text-align: center">' +
                '<button class="btn btn-box-tool btn-add btn-mobile-add" type="button">' +
                    '<i class="fa fa-plus text-blue"></i>' +
                '</button>' +
            '</td>' +
        '</tr>'
    );
    // init iCheck plugin
    page.initICheck();
    // refresh add/remove buttons next to mobile numbers
    $tbody.find('tr:not(:last) .btn-mobile-add')
        .removeClass('btn-mobile-add').addClass('btn-mobile-remove')
        .html('<i class="fa fa-minus text-blue"></i>');
    var $mobile = $tbody.find('tr:last input[class="form-control"]');
    // reinitialize parsley plugin
    $formCustodian.parsley().destroy();
    $mobile.attr('pattern', '/^1[0-9]{10}$/');
    $mobile.attr('required', 'true');
    $formCustodian.parsley();
}).on('click', '.btn-mobile-remove', function (e) {
    // remove the current mobile number
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
var $tbody2 = $("#classTable").find("tbody");
// cancel previously registered add event
$(document).off('click','.btn-class-add');
$(document).on('click', '.btn-class-add', function (e) {
    e.preventDefault();
    var html = $tbody2.find('tr').last().clone();
    html.find('span.select2').remove();
    // insert html for additional relationship
    $tbody2.append(html);
    // init select2 plugin
    page.initSelect2();
    // refresh add/remove buttons
    $tbody2.find('tr:not(:last) .btn-class-add')
        .removeClass('btn-class-add').addClass('btn-class-remove')
        .html('<i class="fa fa-minus text-blue"></i>');
}).on('click', '.btn-class-remove', function (e) {
    // remove the current relationship
    $(this).parents('tr:first').remove();
    e.preventDefault();
    return false;
});

/** 监护人所属部门管理 */
var id = $('#id').val();    // 监护人ID
if (typeof dept === 'undefined') {
    $.getMultiScripts(['js/department.tree.js'], page.siteRoot())
        .done(function() { dept.init('custodians/edit/' + id); })
} else { dept.init('custodians/edit/' + id); }