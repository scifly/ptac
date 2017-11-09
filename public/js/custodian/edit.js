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

/** 监护人学生关系管理 */
var $addPupil = $('#add-pupil');
var $pupils = $('#pupils');
$addPupil.on('click', function () {
    $relationship.val("");
    $('#pupils').modal({backdrop: true});
});

/**
 * 保存选中的学生
 */
var $saveStudent = $('#confirm-bind');
var $studentId = $("#studentId");
var $tBody = $("#tBody");
var $relationship = $("#relationship");

$saveStudent.on('click', function () {
    var student = $studentId.find("option:selected").text().split('-');
    var studentId = $studentId.val();
    var item = $('#tBody tr:last').find('input').val();

    var htm = '<tr>' +
        '<input type="hidden" value="' + studentId + '" name="student_ids[' + item + ']">' +
        '<td>' + student[0] + '</td>' +
        '<td>' + student[1] + '</td>' +
        '<td><input type="text" name="relationships[' + item +  ']" id="" readonly class="no-border" style="background: none" value="' + $relationship.val() + '"></td>' +
        '<td>' +
        '<a href="javascript:" class="delete">' +
        '<i class="fa fa-trash-o text-blue"></i>' +
        '</a>' +
        '</td>' +
        '</tr>';
    $tBody.append(htm);
});
$(document).on('change', '#schoolId', function () {
    var schoolId = $('#schoolId').val();

    var $gradeId = $('#gradeId');
    var $next = $gradeId.next();
    var $prev = $gradeId.prev();

    var $classId = $('#classId');
    var $classNext = $classId.next();
    var $classPrev = $classId.prev();

    var $studentId = $('#studentId');
    var $studentNext = $studentId.next();
    var $studentPrev = $studentId.prev();
    var token = $('#csrf_token').attr('content');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: page.siteRoot() + 'custodians/create?field=school' + '&id=' + schoolId + '&_token=' + token,
        success: function (result) {
            $next.remove();
            $gradeId.remove();
            $prev.after(result['html']['grades']);

            $classNext.remove();
            $classId.remove();
            $classPrev.after(result['html']['classes']);

            $studentNext.remove();
            $studentId.remove();
            $studentPrev.after(result['html']['students']);

            page.initSelect2();
        }
    });
});
$(document).on('change', '#gradeId', function () {
    var gradeId = $('#gradeId').val();

    var $classId = $('#classId');
    var $next = $classId.next();
    var $prev = $classId.prev();

    var $studentId = $('#studentId');
    var $studentNext = $studentId.next();
    var $studentPrev = $studentId.prev();

    var token = $('#csrf_token').attr('content');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: page.siteRoot() + 'custodians/create?field=grade' + '&id=' + gradeId + '&_token=' + token,
        success: function (result) {
            $next.remove();
            $classId.remove();
            $prev.after(result['html']['classes']);

            $studentNext.remove();
            $studentId.remove();
            $studentPrev.after(result['html']['students']);
            page.initSelect2();
        }
    });
});
$(document).on('change', '#classId', function () {
    var classId = $('#classId').val();
    var $studentId = $('#studentId');
    var $next = $studentId.next();
    var $prev = $studentId.prev();
    var token = $('#csrf_token').attr('content');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: page.siteRoot() + 'custodians/create?field=class' + '&id=' + classId + '&_token=' + token,
        success: function (result) {
            $next.remove();
            $studentId.remove();
            $prev.after(result['html']['students']);
            page.initSelect2();
        }
    });
});
//删除监护人
$(document).on('click', '.delete', function () {
    $(this).parents('tr').remove();
});

// /** 监护人所属部门管理 */
// var id = $('#id').val();    // 监护人ID
// if (typeof dept === 'undefined') {
//     $.getMultiScripts(['js/department.tree.js'], page.siteRoot())
//         .done(function() { dept.init('custodians/edit/' + id); })
// } else { dept.init('custodians/edit/' + id); }