$(function () {
    var $form = $('#formUser');
    $form.parsley().on('form:validated', function () {
        var old_password = $('#old_password');
        var pwd1 = $('#password');
         if ($('.parsley-error').length === 0) {
             if (old_password.val() === pwd1.val()) {
                 page.inform('操作结果', '新密码不能不能和原密码相同!', page.failure);
                 pwd1.focus();
                 return false;
             }
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: 'reset',
                data: {
                    password: old_password.val(),
                    pwd: pwd1.val(),
                    _token: $('#csrf_token').attr('content')
                },
                success: function (result) {
                    if (result.statusCode === 400) {
                        $.gritter.add({
                            title: '操作结果',
                            text: '原密码输入不正确,请重新输入',
                            image: '../img/error.png'
                        });
                    } else if (result.statusCode === 200) {
                        $.gritter.add({
                            title: '操作结果',
                            text: '修改成功',
                            image: '../img/confirm.png'
                        });
                        window.location = '../logout';
                    }
                }
            });
        }
    }).on('form:submit', function () {
        return false;
    });
});