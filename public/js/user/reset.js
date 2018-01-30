<<<<<<< HEAD
$('#reset').on('click', function (e) {
    e.preventDefault();

    var password = document.getElementsByName("password")[0];
    var pwd1 = document.getElementsByName('password')[1];
    var pwd2 = document.getElementsByName('password')[2];

    if (password.value === '') {
        page.inform('操作结果', '原密码不能为空!', page.failure);
        password.focus();
        return false;
    }else if (pwd1.replace(/^\s*|\s*$/g, "") === '') {
        page.inform('操作结果', '新密码不正确，请勿使用空格!', page.failure);
        pwd1.focus();
        return false;
    } else if (pwd1.value === '') {
        page.inform('操作结果', '新密码不能为空!', page.failure);
        pwd1.focus();
        return false;
    } else if (password.value === pwd1.value) {
        page.inform('操作结果', '新密码不能不能和原密码相同!', page.failure);
        pwd1.focus();
        return false;
    } else if (pwd2.value === '') {
        page.inform('操作结果', '确认密码不能为空!', page.failure);
        pwd2.focus();
        return false;
    } else if (pwd1.value !== pwd2.value) {
        page.inform('操作结果', '两次密码不一致!', page.failure);
        pwd2.focus();
        return false;
    }

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: 'reset',
        data: {
            password: password.value,
            pwd: pwd1.value,
            _token: $('#csrf_token').attr('content')
        },
        success: function (result) {
            if (result.statusCode === 400) {
                page.inform('操作结果','原密码输入不正确,请重新输入',page.failure);

            }if(result.statusCode === 401) {
                page.inform('操作结果','请填写正确的密码',page.failure);
            } else if (result.statusCode === 200) {
                page.inform('操作结果','修改成功',page.success);
                window.location = '../logout';
            }
=======
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
>>>>>>> refs/remotes/origin/master
        }
    }).on('form:submit', function () {
        return false;
    });
});