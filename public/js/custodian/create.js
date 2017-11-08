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
        '<input class="form-control" placeholder="（请输入手机号码）" name="mobile[' + n + '][mobile]" value="">' +
        '</div>' +
        '</td>' +
        '<td class="text-center">' +
        '<input type="radio" class="minimal" id="mobile[isdefault]" name="mobile[isdefault]" value="' + n + '">' +
        '</td>' +
        '<td class="text-center">' +
        '<input type="checkbox" class="minimal" name="mobile[' + n + '][enabled]">' +
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
var $pupils = $('#pupils');
$addPupil.on('click', function () {
    $('#pupils').modal({backdrop: true});

    // $.ajax({
    //     type: 'GET',
    //     dataType: 'json',
    //     url: page.siteRoot() + 'custodians/create?tabId=' + page.getActiveTabId(),
    //     success: function (result) {
    //         // $pupils.html(result.html);
    //         // console.log(result.js);
    //         // $.getScript(result.js);
    //
    //     },
    //     error: function () {
    //
    //     }
    // });
});

/**
 * 保存选中的学生
 */
var $saveStudent = $('#confirm-bind');
var checkedStudents = $('#department-nodes-checked');
var $studentId = $("#studentId");
var $tBody = $("#tBody");

$saveStudent.on('click', function () {
    var student = $studentId.find("option:selected").text();
    var studentId = $studentId.val();
    var item = checkedStudents.find('button[type=button]').length;
    var htm = '';
    var checkedStudent = '<button type="button" class="btn btn-flat" style="margin-right: 5px;margin-bottom: 5px">' +
        '<i ></i>' + student +
        '<i class="fa fa-close close-selected"></i>' +
        '<input type="hidden" name="selectedStudents[' + item + ']" value="' + studentId + '"/>' +
        '</button>';
    checkedStudents.append(checkedStudent);
    htm = '<tr>' +
        '<td><input type="hidden" value="" name="">张三</td>' +
        '<td><input type="hidden" value="" name="">' + student + '</td>' +
        '<td><input type="text" name="" id="" readonly class="no-border" style="background: none" value="父女"></td>' +
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