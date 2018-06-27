var $form = $('#formUser');

$('.edit_input').on('click', function () {
    var $input = $(this).prevAll('.form-control');
    $input.removeAttr("readonly");
    $input.focus();
    $input.unbind();
    update($input);
});

function update($input) {
    $input.blur(function () {
        var telephone = $('#telephone').val(),
            email = $('#email').val();

        const reg = /^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/;

        if (telephone.length !== 0 && !/^(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}$/.test(telephone)) {
            page.inform('个人信息', '座机号码格式有误,请重填!', page.failure);
            return false;
        }
        if (email.length !== 0 && !reg.test(email)) {
            page.inform('个人信息', '电子邮件格式有误,请重填!', page.failure);
            return false;
        }
        $.ajax({
            type: 'PUT',
            dataType: 'json',
            url: 'profile',
            data: $form.serialize(),
            success: function (result) {
                page.inform(result.title, result.message, page.success);
            },
            error: function (e) {
                page.errorHandler(e);
            }
        });
        $input.attr("readonly", "true");
    });
}
