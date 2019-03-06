var $classId = $('#class_id'),
    $list = $('tbody'),
    empty = $list.html();

// 选择班级
$classId.on('change', function () {
    if ($classId.val() === 0) {
        $list.html(empty);
        return false;
    }
    $('.overlay').show();
    $.ajax({
        type: 'POST',
        dataType: 'html',
        url: 'issue',
        data: {
            _token: page.token(),
            classId: $classId.val()
        },
        success: function (result) { $list.html(result); },
        error: function (e) { page.errorHandler(e); }
    });
});
page.initBackBtn('students');
page.initSelect2();
// 批量发卡
$('#formStudent').parsley().on('form:validated', function () {
    var data = {};
    $('input[name=sn]').each(function () {
        data[$(this).data('uid')] = $(this).val();
    });
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'issue',
        data: {
            _token: page.token(),
            sns: data
        },
        success: function (result) {
            page.inform(result['title'], result['message'], page.success);
        },
        error: function (e) { page.errorHandler(e); }
    });
}).on('form:submit', function () {
    return false;
});
$('input').on('keyup', function() {
    if ($(this).val().length === parseInt($(this).attr('maxlength'))) {
        var i = parseInt($(this).data('seq')) + 1;
        $('input[data-seq=' + i + ']').focus();
    }
});